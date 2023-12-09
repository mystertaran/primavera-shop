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

use Carrier;
use InPostCarrierModel;
use Validate;

class CarrierDataProvider
{
    protected $carriers = [];

    /** @return InPostCarrierModel|null */
    public function getInPostCarrierByCarrierId($id_carrier)
    {
        if (!isset($this->carriers[$id_carrier])) {
            $carrier = new Carrier($id_carrier);

            if (Validate::isLoadedObject($inPostCarrier = new InPostCarrierModel($carrier->id_reference))) {
                $this->carriers[$id_carrier] = $inPostCarrier;
            } else {
                $this->carriers[$id_carrier] = false;
            }
        }

        return $this->carriers[$id_carrier] ?: null;
    }
}
