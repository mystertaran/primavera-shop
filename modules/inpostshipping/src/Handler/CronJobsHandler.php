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

namespace InPost\Shipping\Handler;

use Context;
use InPost\Shipping\Adapter\ToolsAdapter;
use InPost\Shipping\Exception\InvalidActionException;
use InPost\Shipping\Handler\Shipment\UpdateShipmentStatusHandler;
use InPostShipping;

class CronJobsHandler
{
    const ACTION_UPDATE_SHIPMENT_STATUS = 'updateShipmentStatus';

    const ACTIONS = [
        self::ACTION_UPDATE_SHIPMENT_STATUS,
    ];

    protected $module;
    protected $context;
    protected $tools;
    protected $updateShipmentStatusHandler;

    public function __construct(
        InPostShipping $module,
        ToolsAdapter $tools,
        UpdateShipmentStatusHandler $updateShipmentStatusHandler
    ) {
        $this->module = $module;
        $this->context = Context::getContext();
        $this->tools = $tools;
        $this->updateShipmentStatusHandler = $updateShipmentStatusHandler;
    }

    public function handle($action)
    {
        set_time_limit(0);

        switch ($action) {
            case self::ACTION_UPDATE_SHIPMENT_STATUS:
                $this->updateShipmentStatusHandler->handle();
                break;
            default:
                throw new InvalidActionException();
        }
    }

    public function getAvailableActionsUrls()
    {
        $urls = [];

        foreach (self::ACTIONS as $action) {
            $urls[$action] = $this->getActionUrl($action);
        }

        return $urls;
    }

    public function checkToken($token)
    {
        return $token === $this->getToken();
    }

    protected function getActionUrl($action)
    {
        return $this->context->link->getModuleLink($this->module->name, 'cron', [
            'action' => $action,
            'token' => $this->getToken(),
        ]);
    }

    protected function getToken()
    {
        return $this->tools->hash($this->module->name . '_cron');
    }
}
