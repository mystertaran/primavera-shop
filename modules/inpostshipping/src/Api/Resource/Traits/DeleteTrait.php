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

namespace InPost\Shipping\Api\Resource\Traits;

use InPost\Shipping\Api\Resource\ApiResource;

/**
 * @mixin ApiResource
 */
trait DeleteTrait
{
    /**
     * Delete an ApiResource by id.
     *
     * @param string|int $id
     * @param array $options
     *
     * @return void
     */
    public static function delete($id, array $options = [])
    {
        $instance = static::cast([static::getIdField() => $id]);

        $instance->destroy($options);
    }

    /**
     * Delete an ApiResource instance.
     *
     * @param array $options
     *
     * @return void
     */
    public function destroy(array $options = [])
    {
        $this->getRequestFactory()
            ->createRequest('DELETE', static::getResourcePath(), $options)
            ->setPathParams([static::getIdField() => $this->getId()])
            ->send();
    }
}
