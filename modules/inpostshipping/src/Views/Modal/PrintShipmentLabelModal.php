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
use InPost\Shipping\ChoiceProvider\ShipmentLabelFormatChoiceProvider;
use InPost\Shipping\ChoiceProvider\ShipmentLabelTypeChoiceProvider;
use InPost\Shipping\PrestaShopContext;
use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPostShipping;

class PrintShipmentLabelModal extends AbstractModal
{
    const TRANSLATION_SOURCE = 'PrintShipmentLabelModal';

    const MODAL_ID = 'inpost-print-shipment-label-modal';

    protected $labelFormatChoiceProvider;
    protected $labelTypeChoiceProvider;

    public function __construct(
        InPostShipping $module,
        LinkAdapter $link,
        PrestaShopContext $shopContext,
        ShipmentLabelFormatChoiceProvider $labelFormatChoiceProvider,
        ShipmentLabelTypeChoiceProvider $labelTypeChoiceProvider
    ) {
        parent::__construct($module, $link, $shopContext);

        $this->labelFormatChoiceProvider = $labelFormatChoiceProvider;
        $this->labelTypeChoiceProvider = $labelTypeChoiceProvider;

        $this->setTemplate('views/templates/hook/modal/print-shipment-label.tpl');
    }

    protected function getTitle()
    {
        return $this->module->l('Print shipment labels', self::TRANSLATION_SOURCE);
    }

    protected function assignContentTemplateVariables()
    {
        $this->context->smarty->assign([
            'labelFormatChoices' => $this->labelFormatChoiceProvider->getChoices(),
            'defaultLabelFormat' => Shipment::FORMAT_PDF,
            'labelTypeChoices' => $this->labelTypeChoiceProvider->getChoices(),
            'defaultLabelType' => Shipment::TYPE_A6,
        ]);
    }

    protected function getActions()
    {
        return [
            [
                'type' => 'button',
                'value' => 'submitPrintLabel',
                'class' => 'js-submit-print-label-form btn-primary',
                'label' => $this->module->l('Print', self::TRANSLATION_SOURCE),
            ],
        ];
    }
}
