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

namespace InPost\Shipping\Presenter;

use InPost\Shipping\Adapter\LinkAdapter;
use InPost\Shipping\Configuration\CarriersConfiguration;
use InPost\Shipping\ShipX\Resource\Service;
use InPostCarrierModel;

class CarrierPresenter
{
    protected $link;
    protected $carriersConfiguration;

    public function __construct(LinkAdapter $link, CarriersConfiguration $carriersConfiguration)
    {
        $this->link = $link;
        $this->carriersConfiguration = $carriersConfiguration;
    }

    public function present(InPostCarrierModel $inPostCarrier)
    {
        $carrier = $inPostCarrier->getCarrier();

        return [
            'id' => $inPostCarrier->id,
            'service' => $inPostCarrier->service,
            'commercialProductIdentifier' => $inPostCarrier->commercial_product_identifier,
            'cod' => (bool) $inPostCarrier->cod,
            'weekendDelivery' => (bool) $inPostCarrier->weekend_delivery,
            'defaultTemplate' => in_array($inPostCarrier->service, Service::LOCKER_CARRIER_SERVICES, true)
                ? $this->carriersConfiguration->getDefaultDimensionTemplates($inPostCarrier->service)
                : null,
            'defaultDimensions' => $this->carriersConfiguration->getDefaultShipmentDimensions($inPostCarrier->service),
            'defaultSendingMethod' => $this->carriersConfiguration->getDefaultSendingMethods($inPostCarrier->service),
            'useProductDimensions' => (bool) $inPostCarrier->use_product_dimensions,
            'carrier' => $carrier->name,
            'active' => (bool) $carrier->active,
            'editUrl' => $this->link->getAdminLink('AdminCarrierWizard', true, [], [
                'id_carrier' => $carrier->id,
            ]),
        ];
    }
}
