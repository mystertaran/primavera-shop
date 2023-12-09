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

use InPost\Shipping\ChoiceProvider\CarrierChoiceProvider;
use InPost\Shipping\ChoiceProvider\DimensionTemplateChoiceProvider;
use InPost\Shipping\ChoiceProvider\ShippingServiceChoiceProvider;
use InPost\Shipping\Presenter\CarrierPresenter;
use InPost\Shipping\Presenter\Store\PresenterInterface;
use InPostCarrierModel;

class ServicesModule implements PresenterInterface
{
    protected $shippingServiceChoiceProvider;
    protected $carrierChoiceProvider;
    protected $dimensionTemplateChoiceProvider;
    protected $carrierPresenter;

    public function __construct(
        ShippingServiceChoiceProvider $shippingServiceChoiceProvider,
        CarrierChoiceProvider $carrierChoiceProvider,
        DimensionTemplateChoiceProvider $dimensionTemplateChoiceProvider,
        CarrierPresenter $carrierPresenter
    ) {
        $this->shippingServiceChoiceProvider = $shippingServiceChoiceProvider;
        $this->carrierChoiceProvider = $carrierChoiceProvider;
        $this->dimensionTemplateChoiceProvider = $dimensionTemplateChoiceProvider;
        $this->carrierPresenter = $carrierPresenter;
    }

    /**
     * {@inheritdoc}
     */
    public function present()
    {
        return [
            'services' => [
                'choices' => [
                    'service' => $this->shippingServiceChoiceProvider->getChoices(),
                    'carrier' => $this->carrierChoiceProvider->getChoices(),
                    'template' => $this->dimensionTemplateChoiceProvider->getChoices(),
                ],
                'list' => $this->getServiceList(),
            ],
        ];
    }

    protected function getServiceList()
    {
        $list = [];

        foreach (InPostCarrierModel::getNonDeletedCarriers() as $carrier) {
            $list[$carrier->id] = $this->carrierPresenter->present($carrier);
        }

        return $list;
    }
}
