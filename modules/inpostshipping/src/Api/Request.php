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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Query;
use GuzzleHttp\Stream\StreamInterface;

class Request
{
    /**
     * Guzzle Client.
     *
     * @var Client
     */
    protected $client;

    /**
     * HTTP method
     *
     * @var string
     */
    protected $method;

    /**
     * URL path.
     *
     * @var string
     */
    protected $path;

    /**
     * URL path parameters.
     *
     * @var array
     */
    protected $pathParams = [];

    /**
     * Request options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Query aggregator.
     *
     * @var callable
     */
    protected $queryAggregator;

    /**
     * @param Client|null $client Guzzle Client
     */
    public function __construct(Client $client = null)
    {
        if (is_null($client)) {
            $client = new Client();
        }

        $this->client = $client;
    }

    /**
     * Set the HTTP method.
     *
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Set the URL path.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Add Path parameter values.
     * Path parameters encoded in the route URL as '{key}' will be replaced
     * with the appropriate value using the given key/value pairs.
     *
     * @param array $pathParams
     *
     * @return $this
     */
    public function setPathParams(array $pathParams)
    {
        $this->pathParams = array_merge($this->pathParams, $pathParams);

        return $this;
    }

    /**
     * Add request headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->options['headers'] = isset($this->options['headers'])
            ? array_merge($this->options['headers'], $headers)
            : $headers;

        return $this;
    }

    /**
     * Add URL-encoded Query parameter values.
     *
     * @param array $queryParams
     *
     * @return $this
     */
    public function setQueryParams(array $queryParams)
    {
        $this->options['query'] = isset($this->options['query'])
            ? array_merge($this->options['query'], $queryParams)
            : $queryParams;

        return $this;
    }

    /**
     * Set the body value.
     *
     * @param string|resource|StreamInterface $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->options['body'] = $body;

        return $this;
    }

    /**
     * Add request JSON data.
     *
     * @param array $data
     *
     * @return $this
     */
    public function setJson(array $data)
    {
        $this->options['json'] = isset($this->options['json'])
            ? array_merge($this->options['json'], $data)
            : $data;

        return $this;
    }

    /**
     * Set additional Request options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Set query aggregator function.
     *
     * @param callable $aggregator
     *
     * @return $this
     */
    public function setQueryAggregator(callable $aggregator)
    {
        $this->queryAggregator = $aggregator;

        return $this;
    }

    /**
     * Get the Request HTTP method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the Request URL.
     *
     * @return string
     */
    public function getUrl()
    {
        $url = $this->path;

        foreach ($this->pathParams as $key => $value) {
            $url = str_replace("{{$key}}", $value, $url);
        }

        return $url;
    }

    /**
     * Get Request options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get query aggregator.
     *
     * @return callable|null
     */
    public function getQueryAggregator()
    {
        return $this->queryAggregator;
    }

    /**
     * Send the API Request.
     *
     * @return Response
     *
     * @throws RequestException
     */
    public function send()
    {
        $request = $this->client->createRequest(
            $this->getMethod(),
            $this->getUrl(),
            $this->getOptions()
        );

        if ($aggregator = $this->getQueryAggregator()) {
            $request->getQuery()->setAggregator($aggregator);
        }

        return new Response($this->client->send($request));
    }
}
