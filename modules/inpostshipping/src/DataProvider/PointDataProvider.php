<?php
/**
 * Copyright 2021-2023 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright 2021-2023 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 */

namespace InPost\Shipping\DataProvider;

use Context;
use Exception;
use FrontController;
use GuzzleHttp\Exception\RequestException;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\GeoWidget\GeoWidgetTokenProvider;
use InPost\Shipping\Repository\PointRepository;
use InPost\Shipping\ShipX\Exception\AccessForbiddenException;
use InPost\Shipping\ShipX\Exception\InternalServerErrorException;
use InPost\Shipping\ShipX\Exception\ResourceNotFoundException;
use InPost\Shipping\ShipX\Exception\TokenInvalidException;
use InPost\Shipping\ShipX\Resource\NewApiPoint;
use InPost\Shipping\ShipX\Resource\Point;
use InPost\Shipping\Traits\ErrorsTrait;

class PointDataProvider
{
    use ErrorsTrait;

    protected $shipXConfiguration;
    protected $tokenProvider;
    protected $context;
    protected $pointRepository;

    /** @var Point[] */
    protected $points = [];
    protected $useNewApi = true;

    public function __construct(
        ShipXConfiguration $shipXConfiguration,
        GeoWidgetTokenProvider $tokenProvider,
        PointRepository $pointRepository,
        Context $context = null
    ) {
        $this->shipXConfiguration = $shipXConfiguration;
        $this->tokenProvider = $tokenProvider;
        $this->pointRepository = $pointRepository;
        $this->context = isset($context) ? $context : Context::getContext();
    }

    public function getPointData($pointId)
    {
        if (null === $pointId || '' === $pointId) {
            return null;
        }

        if (!array_key_exists($pointId, $this->points)) {
            $this->initPointData($pointId);
        }

        return isset($this->points[$pointId]) ? $this->points[$pointId] : null;
    }

    protected function initPointData($pointId)
    {
        $useSandbox = $useSandboxTmp = $this->shipXConfiguration->useSandboxMode();

        if (
            $this->context->controller instanceof FrontController &&
            $token = $this->tokenProvider->getToken()
        ) {
            $this->shipXConfiguration->setSandboxMode($useSandboxTmp = $token->isSandbox());
        }

        try {
            $cachedPoint = $useSandboxTmp
                ? null
                : $this->pointRepository->findByPointId($pointId);
        } catch (Exception $exception) {
            $cachedPoint = null;
        }

        if ($cachedPoint && $cachedPoint->isFresh()) {
            $this->points[$pointId] = $cachedPoint->getPoint();

            return;
        }

        try {
            $this->points[$pointId] = $this->fetchPoint($pointId);

            if ($useSandboxTmp) {
                return;
            }

            if ($cachedPoint) {
                $this->pointRepository->update($this->points[$pointId]);
            } else {
                $this->pointRepository->insert($this->points[$pointId]);
            }
        } catch (ResourceNotFoundException $exception) {
            $this->points[$pointId] = null;
        } catch (Exception $exception) {
            $this->addError($exception->getMessage());
        } finally {
            $this->shipXConfiguration->setSandboxMode($useSandbox);
        }
    }

    protected function fetchPoint($pointId)
    {
        if (!$this->useNewApi) {
            return Point::get($pointId);
        }

        try {
            return NewApiPoint::get($pointId);
        } catch (Exception $exception) {
            if (
                $exception instanceof AccessForbiddenException ||
                $exception instanceof TokenInvalidException ||
                $exception instanceof InternalServerErrorException ||
                $exception instanceof RequestException
            ) {
                $this->useNewApi = false;

                return $this->fetchPoint($pointId);
            }

            throw $exception;
        }
    }
}
