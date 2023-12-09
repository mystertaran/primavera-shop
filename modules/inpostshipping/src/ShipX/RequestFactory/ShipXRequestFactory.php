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

namespace InPost\Shipping\ShipX\RequestFactory;

use Context;
use GuzzleHttp\Client;
use GuzzleHttp\Query;
use InPost\Shipping\Api\RequestFactoryInterface;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\ShipX\ShipXRequest;
use Tools;

class ShipXRequestFactory implements RequestFactoryInterface
{
    const LIVE_URL = 'https://api-shipx-pl.easypack24.net';
    const SANDBOX_URL = 'https://sandbox-api-shipx-pl.easypack24.net';

    const ALLOWED_LANGUAGES = [
        'pl_PL',
        'en_GB',
        'keys',
    ];

    protected $configuration;

    protected $language;

    public function __construct(ShipXConfiguration $configuration)
    {
        $this->configuration = $configuration;

        $language = Tools::strtolower(Context::getContext()->language->iso_code) === 'pl'
            ? 'pl_PL'
            : 'en_GB';

        $this->setLanguage($language);
    }

    protected function getBaseUrl()
    {
        return $this->configuration->useSandboxMode()
            ? static::SANDBOX_URL
            : static::LIVE_URL;
    }

    public function setLanguage($language)
    {
        if (in_array($language, self::ALLOWED_LANGUAGES)) {
            $this->language = $language;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createRequest($method, $path, array $options = [])
    {
        $client = new Client([
            'base_url' => $this->getBaseUrl(),
        ]);

        return (new ShipXRequest($client))
            ->setOptions($options)
            ->setMethod($method)
            ->setPath($path)
            ->setQueryAggregator(Query::phpAggregator(false))
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Accept-Language' => $this->language,
            ]);
    }
}
