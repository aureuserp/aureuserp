<?php

return [
    'navigation' => [
        'title' => 'Bills of Materials',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'reference'             => 'Reference',
                    'reference-placeholder' => 'eg. BOM-001',
                    'product'               => 'Product',
                    'product-variant'       => 'Product Variant',
                    'quantity'              => 'Quantity',
                    'uom'                   => 'UOM',
                    'operation-type'        => 'Operation Type',
                    'company'               => 'Company',
                    'type'                  => 'BoM Type',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Miscellaneous',
                'fields' => [
                    'kit-information'                         => 'Kit Information',
                    'kit-information-content'                 => 'A kit BoM is used to group components for transfers or sales, instead of being produced through a manufacturing order.',
                    'manufacturing-lead-time'                 => 'Manufacturing Lead Time',
                    'days-to-prepare-manufacturing-order'     => 'Days to prepare Manufacturing Order',
                    'days-suffix'                             => 'days',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'      => 'Components',
                'add-action' => 'Add a line',
                'columns'    => [
                    'component'              => 'Component',
                    'apply-on-variants'      => 'Apply on Variants',
                    'consumed-in-operation'  => 'Consumed in Operation',
                    'highlight-consumption'  => 'Highlight Consumption',
                    'quantity'               => 'Quantity',
                    'uom'                    => 'Product Unit of Measure',
                ],
                'validation' => [
                    'component-different-from-product' => 'The component must be different from the product being manufactured.',
                ],
                'create-form' => [
                    'fields' => [
                        'name'            => 'Name',
                        'type'            => 'Type',
                        'category'        => 'Category',
                        'company'         => 'Company',
                        'uom'             => 'UOM',
                        'uom-placeholder' => 'UOM',
                    ],
                ],
            ],
            'operations' => [
                'title'      => 'Operations',
                'add-action' => 'Add a line',
                'actions'    => [
                    'edit'                 => 'Edit Operation',
                    'copy-existing'        => 'Copy Existing Operations',
                    'copy-existing-fields' => [
                        'operation' => 'Operation',
                    ],
                ],
                'columns'    => [
                    'operation'        => 'Operation',
                    'work-center'      => 'Work Center',
                    'time-mode'        => 'Duration Computation',
                    'time-mode-batch'  => 'Computed on last',
                    'company'          => 'Company',
                    'apply-on-variants'=> 'Apply on Variants',
                    'duration'         => 'Duration (minutes)',
                ],
            ],
            'by-products' => [
                'title'      => 'By-products',
                'add-action' => 'Add a line',
                'columns'    => [
                    'product'   => 'By-product',
                    'quantity'  => 'Quantity',
                    'uom'       => 'Unit of Measure',
                    'operation' => 'Produced in Operation',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Miscellaneous',
                'fields' => [
                    'ready-to-produce'       => 'Manufacturing Readiness',
                    'routing'                => 'Routing',
                    'consumption'            => 'Flexible Consumption',
                    'operation-dependencies' => 'Operation Dependencies',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'  => 'Reference',
            'product'    => 'Product',
            'quantity'   => 'Quantity',
            'uom'        => 'UOM',
            'type'       => 'BOM Type',
            'company'    => 'Company',
            'deleted-at' => 'Deleted At',
            'updated-at' => 'Updated At',
        ],
        'filters' => [
            'product' => 'Product',
            'type'    => 'BOM Type',
            'company' => 'Company',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bill of material restored',
                    'body'  => 'The bill of material has been restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Bill of material archived',
                    'body'  => 'The bill of material has been archived successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Bill of material deleted',
                        'body'  => 'The bill of material has been permanently deleted.',
                    ],
                    'error' => [
                        'title' => 'Bill of material could not be deleted',
                        'body'  => 'The bill of material cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bills of material restored',
                    'body'  => 'The selected bills of material have been restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Bills of material archived',
                    'body'  => 'The selected bills of material have been archived successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Bills of material deleted',
                        'body'  => 'The selected bills of material have been permanently deleted.',
                    ],
                    'error' => [
                        'title' => 'Bills of material could not be deleted',
                        'body'  => 'One or more selected bills of material are currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General Information',
                'entries' => [
                    'reference'      => 'Reference',
                    'product'        => 'Product',
                    'product-variant'=> 'Product Variant',
                    'quantity'       => 'Quantity',
                    'uom'            => 'UOM',
                    'operation-type' => 'Operation Type',
                    'company'        => 'Company',
                    'type'           => 'BoM Type',
                ],
            ],
            'record-information' => [
                'title'   => 'Record Information',
                'entries' => [
                    'created-by'   => 'Created By',
                    'created-at'   => 'Created At',
                    'last-updated' => 'Last Updated',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'   => 'Components',
                'entries' => [
                    'component' => 'Component',
                    'operation' => 'Operation',
                    'quantity'  => 'Quantity',
                    'uom'       => 'Product Unit of Measure',
                ],
            ],
            'operations' => [
                'title'   => 'Operations',
                'entries' => [
                    'operation'   => 'Operation',
                    'work-center' => 'Work Center',
                    'time-mode'   => 'Duration Computation',
                    'duration'    => 'Duration (minutes)',
                ],
            ],
            'by-products' => [
                'title'   => 'By-products',
                'entries' => [
                    'product'   => 'By-product',
                    'quantity'  => 'Quantity',
                    'uom'       => 'Unit of Measure',
                    'operation' => 'Produced in Operation',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Miscellaneous',
                'entries' => [
                    'kit-information'                         => 'Kit Information',
                    'kit-information-content'                 => 'A kit BoM is used to group components for transfers or sales, instead of being produced through a manufacturing order.',
                    'ready-to-produce'                        => 'Manufacturing Readiness',
                    'routing'                                 => 'Routing',
                    'consumption'                             => 'Flexible Consumption',
                    'operation-dependencies'                  => 'Operation Dependencies',
                    'manufacturing-lead-time'                 => 'Manufacturing Lead Time',
                    'days-to-prepare-manufacturing-order'     => 'Days to prepare Manufacturing Order',
                    'days-suffix'                             => 'days',
                ],
            ],
        ],
    ],
];
