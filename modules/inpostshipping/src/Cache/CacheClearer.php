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

namespace InPost\Shipping\Cache;

use Module;
use PrestaShop\ModuleLibCacheDirectoryProvider\Cache\CacheDirectoryProvider;

class CacheClearer
{
    const CONTAINERS = [
        'admin',
        'front',
    ];

    protected $module;
    protected $cacheDirectory;

    public function __construct(Module $module)
    {
        $this->module = $module;

        $this->cacheDirectory = new CacheDirectoryProvider(
            _PS_VERSION_,
            _PS_ROOT_DIR_,
            false
        );
    }

    public function clear()
    {
        foreach (self::CONTAINERS as $containerName) {
            $containerFilePath = sprintf(
                '%s/%s%sContainer.php',
                rtrim($this->cacheDirectory->getPath(), '/'),
                ucfirst($this->module->name),
                ucfirst($containerName)
            );

            if (file_exists($containerFilePath)) {
                unlink($containerFilePath);
            }
        }
    }
}
