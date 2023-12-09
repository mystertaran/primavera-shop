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

namespace InPost\Shipping\Adapter;

use Context;
use InPost\Shipping\PrestaShopContext;
use PrestaShopException;

class LinkAdapter
{
    protected $shopContext;
    protected $link;

    public function __construct(PrestaShopContext $shopContext)
    {
        $this->shopContext = $shopContext;
        $this->link = Context::getContext()->link;
    }

    /**
     * Adapter for getAdminLink from the core Link class
     *
     * @param string $controller controller name
     * @param bool $withToken include the token in the url
     * @param array $sfRouteParams Symfony route parameters
     * @param array $params query parameters
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    public function getAdminLink($controller, $withToken = true, $sfRouteParams = [], $params = [])
    {
        if ($this->shopContext->is17()) {
            return $this->link->getAdminLink($controller, $withToken, $sfRouteParams, $params);
        }

        $paramsAsString = '';
        foreach ($params as $key => $value) {
            $paramsAsString .= "&$key=$value";
        }

        return __PS_BASE_URI__
            . basename(_PS_ADMIN_DIR_) . '/'
            . $this->link->getAdminLink($controller, $withToken)
            . $paramsAsString;
    }
}
