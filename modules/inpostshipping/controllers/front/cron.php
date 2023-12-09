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

use InPost\Shipping\Exception\InvalidActionException;
use InPost\Shipping\Handler\CronJobsHandler;
use InPost\Shipping\ShipX\Exception\ShipXException;

class InPostShippingCronModuleFrontController extends ModuleFrontController
{
    const TRANSLATION_SOURCE = 'cron';

    /** @var InPostShipping */
    public $module;

    /** @var CronJobsHandler */
    protected $cronJobsHandler;

    public function init()
    {
        parent::init();

        $this->cronJobsHandler = $this->module->getService('inpost.shipping.handler.cron_jobs');
    }

    public function postProcess()
    {
        if ($this->cronJobsHandler->checkToken(Tools::getValue('token'))) {
            try {
                $this->cronJobsHandler->handle(Tools::getValue('action'));

                exit($this->module->l('Job complete', self::TRANSLATION_SOURCE));
            } catch (InvalidActionException $exception) {
                exit($this->module->l('Unknown action', self::TRANSLATION_SOURCE));
            } catch (ShipXException $exception) {
                exit(sprintf(
                    $this->module->l('Job failed: %s', self::TRANSLATION_SOURCE),
                    $exception->getMessage()
                ));
            }
        } else {
            exit($this->module->l('Invalid token', self::TRANSLATION_SOURCE));
        }
    }
}
