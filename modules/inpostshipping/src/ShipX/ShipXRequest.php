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

namespace InPost\Shipping\ShipX;

use GuzzleHttp\Exception\RequestException;
use InPost\Shipping\Api\Request;
use InPost\Shipping\Api\Response;
use InPost\Shipping\ShipX\Exception\AccessForbiddenException;
use InPost\Shipping\ShipX\Exception\InternalServerErrorException;
use InPost\Shipping\ShipX\Exception\LabelNotFoundException;
use InPost\Shipping\ShipX\Exception\ResourceNotFoundException;
use InPost\Shipping\ShipX\Exception\ShipXException;
use InPost\Shipping\ShipX\Exception\TokenInvalidException;
use InPost\Shipping\ShipX\Exception\ValidationFailedException;

class ShipXRequest extends Request
{
    /**
     * Send the API Request.
     *
     * @return Response
     *
     * @throws RequestException
     * @throws ShipXException
     */
    public function send()
    {
        try {
            return parent::send();
        } catch (RequestException $exception) {
            throw $this->wrapException($exception);
        }
    }

    protected function wrapException(RequestException $exception)
    {
        if ($exception->hasResponse()) {
            $exception->getResponse()->getBody()->seek(0);
            $response = json_decode($exception->getResponse()->getBody()->getContents(), true);

            if (isset($response['error'])) {
                return $this->getExceptionByErrorCode($response, $exception);
            }
        }

        return $exception;
    }

    protected function getExceptionByErrorCode(array $response, RequestException $exception)
    {
        switch ($response['error']) {
            case 'access_forbidden':
            case 'Forbidden':
                return new AccessForbiddenException($response, $exception);
            case 'resource_not_found':
                return new ResourceNotFoundException($response, $exception);
            case 'token_invalid':
                return new TokenInvalidException($response, $exception);
            case 'validation_failed':
                return new ValidationFailedException($response, $exception);
            case 'label_not_found':
                return new LabelNotFoundException($response, $exception);
            case 'Internal Server Error':
                return new InternalServerErrorException($response, $exception);
            default:
                return new ShipXException($response, $exception);
        }
    }
}
