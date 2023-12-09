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

use InPost\Shipping\PrestaShopContext;
use Language;

class LanguageDataProvider
{
    protected $shopContext;

    protected $languages;
    protected $locales;

    public function __construct(PrestaShopContext $shopContext)
    {
        $this->shopContext = $shopContext;
    }

    public function getLanguages()
    {
        $this->initLanguages();

        return $this->languages;
    }

    public function getLocaleById($id_lang)
    {
        $this->initLanguages();

        return isset($this->languages[$id_lang]) ? $this->languages[$id_lang]['locale'] : null;
    }

    protected function initLanguages()
    {
        if (!isset($this->languages)) {
            foreach (Language::getLanguages(false) as $language) {
                if (!$this->shopContext->is17()) {
                    $language['locale'] = $language['iso_code'];
                }

                $this->languages[$language['id_lang']] = $language;
            }
        }
    }
}
