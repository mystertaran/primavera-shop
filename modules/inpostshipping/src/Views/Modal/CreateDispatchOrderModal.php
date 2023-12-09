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

namespace InPost\Shipping\Views\Modal;

use InPost\Shipping\Adapter\LinkAdapter;
use InPost\Shipping\ChoiceProvider\DispatchPointChoiceProvider;
use InPost\Shipping\Configuration\SendingConfiguration;
use InPost\Shipping\Install\Tabs;
use InPost\Shipping\PrestaShopContext;
use InPostDispatchPointModel;
use InPostShipping;

class CreateDispatchOrderModal extends AbstractModal
{
    const TRANSLATION_SOURCE = 'CreateDispatchOrderModal';

    const MODAL_ID = 'inpost-create-dispatch-order-modal';

    protected $dispatchPointChoiceProvider;
    protected $sendingConfiguration;

    public function __construct(
        InPostShipping $module,
        LinkAdapter $link,
        PrestaShopContext $shopContext,
        DispatchPointChoiceProvider $dispatchPointChoiceProvider,
        SendingConfiguration $sendingConfiguration
    ) {
        parent::__construct($module, $link, $shopContext);

        $this->dispatchPointChoiceProvider = $dispatchPointChoiceProvider;
        $this->sendingConfiguration = $sendingConfiguration;

        $this->setTemplate('views/templates/hook/modal/create-dispatch-order.tpl');
    }

    protected function getTitle()
    {
        return $this->module->l('Create dispatch order', self::TRANSLATION_SOURCE);
    }

    protected function assignContentTemplateVariables()
    {
        $this->context->smarty->assign([
            'newDispatchPointUrl' => $this->link->getAdminLink(Tabs::DISPATCH_POINT_CONTROLLER_NAME, true, [], [
                'add' . InPostDispatchPointModel::$definition['table'] => true,
            ]),
            'dispatchOrderAction' => $this->link->getAdminLink(Tabs::SHIPMENTS_CONTROLLER_NAME, true, [], [
                'action' => 'createDispatchOrder',
            ]),
            'dispatchPointChoices' => $this->dispatchPointChoiceProvider->getChoices(),
            'defaultDispatchPoint' => $this->sendingConfiguration->getDefaultDispatchPointId(),
        ]);

        return $this;
    }

    protected function getActions()
    {
        return [
            [
                'type' => 'button',
                'value' => 'submitDispatchOrder',
                'class' => 'js-submit-dispatch-order-form btn-primary',
                'label' => $this->module->l('Submit', self::TRANSLATION_SOURCE),
            ],
        ];
    }
}
