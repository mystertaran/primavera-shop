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

use InPost\Shipping\ChoiceProvider\ShippingServiceChoiceProvider;
use InPost\Shipping\ShipX\Resource\Service;
use InPost\Shipping\Traits\ErrorsTrait;
use InPostProductTemplateModel;
use InPostShipping;
use Product;
use Validate;

class ProductUpdateHandler
{
    use ErrorsTrait;

    const TRANSLATION_SOURCE = 'ProductUpdateHandler';

    protected $module;
    protected $serviceChoiceProvider;

    public function __construct(
        InPostShipping $module,
        ShippingServiceChoiceProvider $serviceChoiceProvider
    ) {
        $this->module = $module;
        $this->serviceChoiceProvider = $serviceChoiceProvider;
    }

    public function update(Product $product, $template)
    {
        $this->resetErrors();

        $productTemplate = new InPostProductTemplateModel($product->id);

        if ($template !== null) {
            if (!in_array($template, $this->serviceChoiceProvider->getAvailableTemplates(Service::INPOST_LOCKER_STANDARD))) {
                $this->addError(
                    $this->module->l('Selected InPost shipment dimension template is not valid', self::TRANSLATION_SOURCE)
                );
            } else {
                $productTemplate->template = $template;
                if (Validate::isLoadedObject($productTemplate)) {
                    if (!$productTemplate->update()) {
                        $this->addError(
                            $this->module->l('Could not update default InPost shipment dimension template', self::TRANSLATION_SOURCE)
                        );
                    }
                } else {
                    $productTemplate->id = $product->id;
                    if (!$productTemplate->add()) {
                        $this->addError(
                            $this->module->l('Could not update default InPost shipment dimension template', self::TRANSLATION_SOURCE)
                        );
                    }
                }
            }
        } elseif (Validate::isLoadedObject($productTemplate) && !$productTemplate->delete()) {
            $this->addError(
                $this->module->l('Could not remove default InPost shipment dimension template', self::TRANSLATION_SOURCE)
            );
        }

        return !$this->hasErrors();
    }
}
