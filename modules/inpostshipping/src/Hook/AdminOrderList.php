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

namespace InPost\Shipping\Hook;

use Doctrine\DBAL\Query\QueryBuilder;
use InPost\Shipping\Adapter\LinkAdapter;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\Install\Tabs;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\ButtonBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;

class AdminOrderList extends AbstractAdminOrdersHook
{
    const HOOK_LIST = [
        'actionAdminOrdersListingFieldsModifier',
        'displayAdminOrdersListBefore',
        'displayAdminOrdersListAfter',
    ];

    const HOOK_LIST_177 = [
        'displayAdminGridTableBefore',
        'actionOrderGridDefinitionModifier',
        'actionOrderGridQueryBuilderModifier',
    ];

    const TRANSLATION_SOURCE = 'AdminOrderList';

    public function hookActionAdminOrdersListingFieldsModifier($params)
    {
        if (isset($params['select'])) {
            /* unless there's a comma after column name/alias AdminController::getList appends field to query */
            $params['select'] = 'MAX(inpost.date_add) as inpost_shipment_date, ' . $params['select'];
            $params['join'] .= ' LEFT JOIN `' . _DB_PREFIX_ . 'inpost_shipment` inpost
                ON inpost.id_order = a.id_order AND inpost.sandbox = ' . $this->getSandbox();
            $params['group_by'] = 'GROUP BY a.id_order';
        }

        $params['fields']['reference']['filter_key'] = 'a!reference';
        $params['fields'] = array_merge($params['fields'], [
            'inpost_shipment_date' => [
                'title' => $this->module->l('InPost date', self::TRANSLATION_SOURCE),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'inpost!date_add',
            ],
        ]);
    }

    public function hookDisplayAdminOrdersListBefore()
    {
        return $this->renderPrintShipmentLabelModal() . $this->renderDispatchOrderModal();
    }

    public function hookDisplayAdminOrdersListAfter()
    {
        $this->context->smarty->assign([
            'moduleDisplayName' => $this->module->displayName,
            'bulkActions' => $this->getBulkActions(),
        ]);

        return $this->module->display($this->module->name, 'views/templates/hook/admin-orders-list-after.tpl');
    }

    public function hookDisplayAdminGridTableBefore($params)
    {
        if ($params['legacy_controller'] === 'AdminOrders') {
            return '<div id="ajaxBox" class="mt-3"></div>'
                . $this->renderPrintShipmentLabelModal()
                . $this->renderDispatchOrderModal();
        }

        return '';
    }

    public function hookActionOrderGridDefinitionModifier($params)
    {
        /** @var GridDefinitionInterface $definition */
        $definition = $params['definition'];

        $definition->getColumns()
            ->addAfter(
                'date_add',
                (new DateTimeColumn('inpost_shipment_date'))
                    ->setName($this->module->l('InPost shipment creation date', self::TRANSLATION_SOURCE))
                    ->setOptions([
                        'field' => 'inpost_shipment_date',
                        'empty_data' => '--',
                    ])
            );

        $definition->getFilters()
            ->add(
                (new Filter('inpost_shipment_date', DateRangeType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->module->l('Search date', self::TRANSLATION_SOURCE),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('inpost_shipment_date')
            );

        $bulkActions = $definition->getBulkActions();
        foreach ($this->getBulkActions() as $key => $bulkAction) {
            $bulkActions->add(
                (new ButtonBulkAction($key))
                    ->setName($bulkAction['label'])
                    ->setOptions([
                        'class' => $bulkAction['class'],
                        'attributes' => [
                            'data-action' => $bulkAction['action'],
                        ],
                    ])
            );
        }
    }

    public function hookActionOrderGridQueryBuilderModifier($params)
    {
        /** @var QueryBuilder[] $queryBuilders */
        $queryBuilders = [
            'search' => $params['search_query_builder'],
            'count' => $params['count_query_builder'],
        ];

        /** @var OrderFilters $searchCriteria */
        $searchCriteria = $params['search_criteria'];
        $filters = $searchCriteria->getFilters();

        foreach ($queryBuilders as $queryBuilder) {
            if (isset($filters['inpost_shipment_date']['from'])) {
                $queryBuilder
                    ->andWhere('inpost.date_add >= :inpost_date_from')
                    ->setParameter('inpost_date_from', $filters['inpost_shipment_date']['from']);
            }
            if (isset($filters['inpost_shipment_date']['to'])) {
                $queryBuilder
                    ->andWhere('inpost.date_add <= :inpost_date_to')
                    ->setParameter('inpost_date_to', $filters['inpost_shipment_date']['to']);
            }
        }

        if (!isset($filters['inpost_shipment_date']['from']) && !isset($filters['inpost_shipment_date']['to'])) {
            unset($queryBuilders['count']);
        }

        foreach ($queryBuilders as $queryBuilder) {
            $queryBuilder
                ->addSelect(sprintf(
                    'IFNULL(MAX(inpost.date_add), "%s") as inpost_shipment_date',
                    DateTime::NULL_VALUE
                ))
                ->leftJoin(
                    'o',
                    _DB_PREFIX_ . 'inpost_shipment',
                    'inpost',
                    'inpost.id_order = o.id_order AND inpost.sandbox = ' . $this->getSandbox()
                )
                ->groupBy('o.id_order');
        }
    }

    protected function getBulkActions()
    {
        /** @var LinkAdapter $link */
        $link = $this->module->getService('inpost.shipping.adapter.link');

        return [
            'inpost_create_shipments' => [
                'label' => $this->module->l('Create InPost shipments', self::TRANSLATION_SOURCE),
                'action' => $link->getAdminLink(Tabs::SHIPMENTS_CONTROLLER_NAME, true, [], [
                    'action' => 'bulkCreateShipment',
                    'ajax' => true,
                ]),
                'class' => 'js-inpost-bulk-create-shipments',
                'icon' => 'truck',
            ],
            'inpost_create_print_shipments' => [
                'label' => $this->module->l('Create InPost shipments and print labels', self::TRANSLATION_SOURCE),
                'action' => $link->getAdminLink(Tabs::SHIPMENTS_CONTROLLER_NAME, true, [], [
                    'action' => 'bulkPrintLabels',
                    'ajax' => true,
                ]),
                'class' => 'js-inpost-bulk-create-print-shipments',
                'icon' => 'print',
            ],
            'inpost_create_dispatch_orders' => [
                'label' => $this->module->l('Create InPost dispatch orders', self::TRANSLATION_SOURCE),
                'action' => $link->getAdminLink(Tabs::SHIPMENTS_CONTROLLER_NAME, true, [], [
                    'action' => 'bulkCreateDispatchOrders',
                    'ajax' => true,
                ]),
                'class' => 'js-inpost-bulk-create-dispatch-orders',
                'icon' => 'truck',
            ],
            'inpost_print_dispatch_orders' => [
                'label' => $this->module->l('Print InPost dispatch orders', self::TRANSLATION_SOURCE),
                'action' => $link->getAdminLink(Tabs::SHIPMENTS_CONTROLLER_NAME, true, [], [
                    'action' => 'bulkPrintDispatchOrders',
                    'ajax' => true,
                ]),
                'class' => 'js-inpost-bulk-print-dispatch-orders',
                'icon' => 'print',
            ],
            'inpost_refresh_shipment_status' => [
                'label' => $this->module->l('Update InPost shipments\' statuses', self::TRANSLATION_SOURCE),
                'action' => $link->getAdminLink(Tabs::SHIPMENTS_CONTROLLER_NAME, true, [], [
                    'action' => 'bulkRefreshStatuses',
                    'ajax' => true,
                ]),
                'class' => 'js-inpost-bulk-refresh-shipment-status',
                'icon' => 'refresh',
            ],
        ];
    }

    protected function getSandbox()
    {
        /** @var ShipXConfiguration $configuration */
        $configuration = $this->module->getService('inpost.shipping.configuration.shipx');

        return $configuration->isSandboxModeEnabled() ? 1 : 0;
    }
}
