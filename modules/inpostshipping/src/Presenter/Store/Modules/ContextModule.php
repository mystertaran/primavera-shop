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

namespace InPost\Shipping\Presenter\Store\Modules;

use Context;
use Currency;
use InPost\Shipping\Adapter\LinkAdapter;
use InPost\Shipping\Configuration\ShopConfiguration;
use InPost\Shipping\Handler\CronJobsHandler;
use InPost\Shipping\Install\Tabs;
use InPost\Shipping\Presenter\Store\PresenterInterface;
use InPost\Shipping\Translations\Translations;
use InPostDispatchPointModel;

class ContextModule implements PresenterInterface
{
    protected $context;
    protected $link;
    protected $translations;
    protected $cronJobHandler;
    protected $shopConfiguration;

    public function __construct(
        Translations $translations,
        LinkAdapter $link,
        CronJobsHandler $cronJobHandler,
        ShopConfiguration $shopConfiguration
    ) {
        $this->link = $link;
        $this->translations = $translations;
        $this->cronJobHandler = $cronJobHandler;
        $this->shopConfiguration = $shopConfiguration;

        $this->context = Context::getContext();
    }

    /**
     * {@inheritdoc}
     */
    public function present()
    {
        $currency = Currency::getDefaultCurrency() ?: null;

        return [
            'context' => [
                'ajaxController' => $this->link->getAdminLink(Tabs::AJAX_CONTROLLER_NAME),
                'dispatchPointsController' => $this->link->getAdminLink(Tabs::DISPATCH_POINT_CONTROLLER_NAME),
                'newDispatchPointUrl' => $this->link->getAdminLink(Tabs::DISPATCH_POINT_CONTROLLER_NAME, true, [], [
                    'add' . InPostDispatchPointModel::$definition['table'] => true,
                ]),
                'locale' => $this->context->language->iso_code,
                'currency' => [
                    'symbol' => $currency ? $currency->getSign() : '',
                    'precision' => $this->getCurrencyPrecision($currency),
                ],
                'translations' => [
                    $this->context->language->iso_code => $this->translations->getTranslations(),
                ],
                'cronUrls' => $this->cronJobHandler->getAvailableActionsUrls(),
            ],
        ];
    }

    protected function getCurrencyPrecision(Currency $currency = null)
    {
        return null !== $currency && property_exists($currency, 'precision')
            ? (int) $currency->precision
            : $this->shopConfiguration->getPriceDisplayPrecision();
    }
}
