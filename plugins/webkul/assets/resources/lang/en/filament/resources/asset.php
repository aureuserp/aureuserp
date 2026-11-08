<?php

return [
    'title'        => 'Company Asset',
    'plural-title' => 'Company Assets',

    'navigation' => [
        'title' => 'Assets',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Asset Details',
                'fields' => [
                    'name'                       => 'Asset Name',
                    'product'                    => 'Product / Model',
                    'product-type'               => 'Product Type',
                    'quantity'                   => 'Quantity',
                    'quantity-help'              => 'Total quantity received into this batch.',
                    'asset-tag'                  => 'Asset Tag',
                    'asset-tag-help'             => 'Unique identifier code for the asset.',
                    'asset-tag-auto-placeholder' => 'AST-XXXXX (Auto-generated)',
                    'serial-no'                  => 'Serial Number',
                    'model'                      => 'Model',
                    'category'                   => 'Category',
                    'condition'                  => 'Condition',
                    'cost'                       => 'Cost',
                    'purchase-date'              => 'Purchase Date',
                    'warranty-until'             => 'Warranty Until',
                    'warehouse'                  => 'Warehouse',
                    'warehouse-help'             => 'Inventory warehouse where this asset is stored when available.',
                    'initial-placement'          => 'Initial Placement',
                    'placements'                 => [
                        'warehouse'        => 'Inventory Warehouse',
                        'site-asset'       => 'Fixed Site Asset',
                        'employee-custody' => 'Employee Custody',
                    ],
                    'current-site'               => 'Site',
                    'current-employee'           => 'Employee',
                    'note'                       => 'Notes',
                ],
            ],

            'settings' => [
                'title'  => 'Settings',
                'fields' => [
                    'company' => 'Company',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'asset-tag'        => 'Asset Tag',
            'name'             => 'Asset Name',
            'product'          => 'Product',
            'product-type'     => 'Product Type',
            'quantity'         => 'Quantity',
            'model'            => 'Model',
            'serial-no'        => 'Serial No.',
            'category'         => 'Category',
            'status'           => 'Status',
            'condition'        => 'Condition',
            'holder'           => 'Current Custody',
            'warehouse'        => 'Warehouse',
            'current-employee' => 'Current Employee',
            'current-site'     => 'Current Site',
            'cost'             => 'Cost',
            'company'          => 'Company',
            'deleted-at'       => 'Archived At',
            'created-at'       => 'Created At',
            'updated-at'       => 'Updated At',
        ],

        'groups' => [
            'product'          => 'Product / Model',
            'category'         => 'Category',
            'status'           => 'Status',
            'condition'        => 'Condition',
            'warehouse'        => 'Warehouse',
            'current-employee' => 'Current Employee',
            'current-site'     => 'Current Site',
        ],

        'filters' => [
            'status'              => 'Status',
            'condition'           => 'Condition',
            'category'            => 'Category',
            'current-holder-type' => 'Custody Type',
            'current-employee'    => 'Current Employee',
            'department'          => 'Department',
            'warehouse'           => 'Warehouse',
            'current-site'        => 'Current Site',
            'site-usage-type'     => 'Site Usage',
            'site-type'           => 'Site Type',
            'product'             => 'Product',
            'product-type'        => 'Product Type',
            'company'             => 'Company',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Asset updated',
                    'body'  => 'The asset has been updated successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Asset could not be deleted',
                    ],
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Export',
            ],
        ],
    ],

    'actions' => [
        'issue' => [
            'label' => 'Issue Custody',
            'modal' => [
                'heading'                => 'Issue Asset Custody',
                'description'            => 'Assign this asset to an employee or site.',
                'consumable-description' => 'Available balance: :quantity units. Quantities are deducted from this batch on issue.',
                'submit'                 => 'Issue Custody',
            ],
            'fields' => [
                'allocation-mode'    => 'Allocation Mode',
                'allocation-modes'   => [
                    'employee-custody' => 'Employee Custody',
                    'site-asset'       => 'Fixed Site Asset',
                ],
                'holder-type'        => 'Custody Type',
                'employee'           => 'Employee',
                'employees'          => 'Recipients',
                'departments'        => 'Departments',
                'departments-help'   => 'Bulk-select recipients by department. Choosing a site or departments fills the recipient list automatically.',
                'recipients-summary' => ':count recipient(s) — :total unit(s) in total',
                'site'               => 'Site',
                'receiver'           => 'Receiver in Field',
                'condition-out'      => 'Condition at Issue',
                'quantity'           => 'Quantity per Recipient',
                'quantity-help'      => 'Each selected recipient receives this quantity. The batch balance is reduced accordingly.',
                'issued-at'          => 'Issue Date & Time',
                'expected-return-at' => 'Expected Return Date',
                'issue-note'         => 'Issue Note',
                'issue-attachments'  => 'Handover Attachments',
            ],
            'notification' => [
                'title' => 'Custody Issued',
                'body'  => 'Asset custody has been issued successfully.',
            ],
        ],

        'transfer' => [
            'label' => 'Transfer Custody',
            'modal' => [
                'heading'     => 'Transfer Asset Custody',
                'description' => 'Transfer this asset to a new employee or site.',
                'submit'      => 'Transfer Custody',
            ],
            'fields' => [
                'allocation-mode'    => 'Allocation Mode',
                'allocation-modes'   => [
                    'employee-custody' => 'Employee Custody',
                    'site-asset'       => 'Fixed Site Asset',
                ],
                'holder-type'        => 'New Custody Type',
                'employee'           => 'New Employee',
                'site'               => 'New Site',
                'receiver'           => 'Receiver in Field',
                'condition-in'       => 'Condition from Previous Holder',
                'issued-at'          => 'Transfer Date & Time',
                'expected-return-at' => 'Expected Return Date',
                'issue-note'         => 'Transfer Note',
                'return-note'        => 'Return Note from Previous Holder',
                'return-attachments' => 'Handover Return Attachments',
                'issue-attachments'  => 'New Handover Attachments',
            ],
            'notification' => [
                'title' => 'Custody Transferred',
                'body'  => 'Asset custody has been transferred successfully.',
            ],
        ],

        'return' => [
            'label' => 'Return Asset',
            'modal' => [
                'heading'     => 'Return Asset to Inventory',
                'description' => 'Receive and check the asset back into available inventory.',
                'submit'      => 'Confirm Return',
            ],
            'fields' => [
                'condition-in'       => 'Condition upon Return',
                'ended-at'           => 'Return Date & Time',
                'warehouse'          => 'Receiving Warehouse',
                'return-note'        => 'Return Note',
                'return-attachments' => 'Return Attachments',
            ],
            'notification' => [
                'title' => 'Asset Returned',
                'body'  => 'The asset has been returned to available inventory.',
            ],
        ],

        'scrap' => [
            'label' => 'Scrap Asset',
            'modal' => [
                'heading'     => 'Scrap Asset',
                'description' => 'Mark this asset as scrapped. This closes any open custody.',
                'submit'      => 'Confirm Scrap',
            ],
            'fields' => [
                'ended-at'    => 'Scrap Date & Time',
                'note'        => 'Scrap Reason / Note',
                'attachments' => 'Scrap Attachments',
            ],
            'notification' => [
                'title' => 'Asset Scrapped',
                'body'  => 'The asset has been marked as scrapped.',
            ],
        ],

        'mark-lost' => [
            'label' => 'Mark Lost',
            'modal' => [
                'heading'     => 'Mark Asset as Lost',
                'description' => 'Mark this asset as lost. This closes any open custody.',
                'submit'      => 'Confirm Lost',
            ],
            'fields' => [
                'ended-at'    => 'Reported Date & Time',
                'note'        => 'Incident / Investigation Note',
                'attachments' => 'Report Attachments',
            ],
            'notification' => [
                'title' => 'Asset Marked Lost',
                'body'  => 'The asset has been marked as lost.',
            ],
        ],

        'bulk-register' => [
            'label' => 'Bulk Register Units',
            'modal' => [
                'heading'     => 'Bulk Register Asset Units',
                'description' => 'Register multiple asset units under the same model in one batch.',
                'submit'      => 'Register Units',
            ],
            'sections' => [
                'shared'  => 'Shared Model & Batch Details',
                'mode'    => 'Tracking Mode & Input',
                'preview' => 'Validation Preview',
            ],
            'fields' => [
                'company'             => 'Company',
                'product'             => 'Product / Model',
                'name'                => 'Asset Name',
                'model'               => 'Model',
                'category'            => 'Category',
                'condition'           => 'Initial Condition',
                'cost'                => 'Unit Cost',
                'purchase-date'       => 'Purchase Date',
                'warranty-until'      => 'Warranty Until',
                'warehouse'           => 'Warehouse',
                'warehouse-help'      => 'Inventory warehouse where the registered available assets are stored.',
                'initial-placement'   => 'Initial Placement',
                'placements'          => [
                    'warehouse'        => 'Inventory Warehouse',
                    'site-asset'       => 'Fixed Site Asset',
                    'employee-custody' => 'Employee Custody',
                ],
                'site'                => 'Site',
                'employee'            => 'Employee',
                'attachments'         => 'Handover Attachments',
                'note'                => 'Batch Note',
                'tracking-mode'       => 'Tracking Mode',
                'modes'               => [
                    'with-serials'    => 'Serialized Units (Tracked by Serial Number)',
                    'without-serials' => 'Un-numbered Units (Office chairs, tools, bulk items)',
                ],
                'serials-raw'         => 'Serials List (one per line or comma-separated)',
                'serials-raw-help'    => 'Paste serials. Format: SERIAL_NO or TAG,SERIAL_NO per line.',
                'parse-to-table'      => 'Parse into Table',
                'items'               => 'Unit Entries',
                'serial-no'           => 'Serial Number',
                'asset-tag'           => 'Asset Tag',
                'quantity'            => 'Quantity to Generate',
                'tags-raw'            => 'Asset Tags (one per line)',
                'tags-raw-help'       => 'Pre-printed asset tags for un-numbered items.',
                'unit-note'           => 'Unit Note',
            ],
            'preview' => [
                'valid'      => ':count Valid Units',
                'duplicates' => ':count In-Batch Duplicates',
                'collisions' => ':count Existing in DB',
            ],
            'notification' => [
                'title' => 'Bulk Registration Successful',
                'body'  => ':count asset units have been registered successfully.',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'identity' => [
                'title'   => 'Asset Identity',
                'entries' => [
                    'asset-tag' => 'Asset Tag',
                    'name'      => 'Asset Name',
                    'product'   => 'Product',
                    'model'     => 'Model',
                    'category'  => 'Category',
                    'serial-no' => 'Serial Number',
                    'status'    => 'Status',
                    'condition' => 'Condition',
                ],
            ],

            'custody' => [
                'title'   => 'Current Custody',
                'entries' => [
                    'current-employee'   => 'Current Employee',
                    'current-site'       => 'Current Site',
                    'site-usage'         => 'Site Usage',
                    'warehouse'          => 'Warehouse',
                    'initial-quantity'   => 'Initial Quantity',
                    'available-quantity' => 'Available Quantity',
                    'issued-at'          => 'Issued At',
                    'expected-return-at' => 'Expected Return',
                    'custody-note'       => 'Custody Note',
                ],
            ],

            'acquisition' => [
                'title'   => 'Acquisition & Financials',
                'entries' => [
                    'company'        => 'Company',
                    'purchase-date'  => 'Purchase Date',
                    'warranty-until' => 'Warranty Until',
                    'cost'           => 'Cost',
                    'creator'        => 'Created By',
                ],
            ],

            'notes' => [
                'title'   => 'Notes',
                'entries' => [
                    'note' => 'Notes',
                ],
            ],

            'audit' => [
                'title'   => 'Audit',
                'entries' => [
                    'created-at' => 'Created At',
                    'updated-at' => 'Updated At',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'assignments' => [
            'title'   => 'Custody & Movement History',
            'columns' => [
                'occurred-at'        => 'Date & Time',
                'type'               => 'Movement Type',
                'source-type'        => 'Source',
                'from-display'       => 'From',
                'to-display'         => 'To',
                'condition'          => 'Condition',
                'performed-by'       => 'Performed By',
                'expected-return-at' => 'Expected Return',
                'note'               => 'Note',
                'attachments'        => 'Attachments',
            ],
            'filters' => [
                'type'        => 'Movement Type',
                'source-type' => 'Source',
            ],
            'view-modal' => [
                'heading'  => 'Movement Details',
                'sections' => [
                    'details'     => 'Movement Information',
                    'attachments' => 'Attachments',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'New Asset',
                    'notification' => [
                        'title' => 'Asset created',
                        'body'  => 'The asset has been created successfully.',
                    ],
                ],
            ],
            'tabs' => [
                'all'            => 'All',
                'available'      => 'Available',
                'consumables'    => 'Consumables',
                'site-assets'    => 'Fixed Site Assets',
                'with-employees' => 'With Employees',
                'at-sites'       => 'At Sites / Warehouses',
                'in-the-field'   => 'In the Field',
                'overdue-return' => 'Overdue Return',
                'scrapped-lost'  => 'Scrapped / Lost',
                'archived'       => 'Archived',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Asset created',
                'body'  => 'The asset has been created successfully.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Asset updated',
                'body'  => 'The asset has been updated successfully.',
            ],
        ],
    ],
];
