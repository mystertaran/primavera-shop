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

namespace InPost\Shipping\Api;

use GuzzleHttp\Message\ResponseInterface;
use stdClass;

class Response
{
    /**
     * Response from Guzzle.
     *
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Get response contents as string.
     *
     * @return string
     */
    public function getContents()
    {
        $this->response->getBody()->seek(0);

        return $this->response->getBody()->getContents();
    }

    /**
     * Decode response JSON.
     *
     * @param bool $associative
     *
     * @return array|stdClass|null
     */
    public function json($associative = true)
    {
        return json_decode($this->getContents(), $associative);
    }

    public function getHeader($header)
    {
        return $this->response->getHeader($header);
    }
}
