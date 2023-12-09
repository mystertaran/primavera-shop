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

use InPost\Shipping\ChoiceProvider\DispatchPointChoiceProvider;
use InPost\Shipping\ChoiceProvider\SendingMethodChoiceProvider;
use InPost\Shipping\DataProvider\OrganizationDataProvider;
use InPost\Shipping\Presenter\Store\PresenterInterface;

class OrganizationModule implements PresenterInterface
{
    protected $organizationDataProvider;
    protected $sendingMethodChoiceProvider;
    protected $dispatchPointChoiceProvider;

    protected $organization;

    protected $refreshError;

    public function __construct(
        OrganizationDataProvider $organizationDataProvider,
        SendingMethodChoiceProvider $sendingMethodChoiceProvider,
        DispatchPointChoiceProvider $dispatchPointChoiceProvider
    ) {
        $this->organizationDataProvider = $organizationDataProvider;
        $this->sendingMethodChoiceProvider = $sendingMethodChoiceProvider;
        $this->dispatchPointChoiceProvider = $dispatchPointChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function present()
    {
        return [
            'organization' => [
                'details' => $this->organizationDataProvider->getOrganizationData(),
                'choices' => [
                    'sendingMethod' => $this->sendingMethodChoiceProvider->getChoices(),
                    'dispatchPoint' => $this->dispatchPointChoiceProvider->getChoices(),
                ],
                'apiErrors' => $this->organizationDataProvider->getErrors(),
            ],
        ];
    }
}
