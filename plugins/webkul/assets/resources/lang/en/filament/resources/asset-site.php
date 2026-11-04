<?php

return [
    'title'        => 'Asset & Custody Site',
    'plural-title' => 'Asset & Custody Sites',

    'navigation' => [
        'title' => 'Asset & Custody Sites',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Site Details',
                'fields' => [
                    'name'              => 'Name',
                    'parent'            => 'Parent Site',
                    'usage-type'        => 'Usage Type',
                    'usage-type-help'   => 'Fixed Asset Locations hold company-deployed assets; Custody Zones classify personal employee custody.',
                    'code'              => 'Code',
                    'code-help'         => 'Optional internal code, unique within the company.',
                    'type'              => 'Type',
                    'type-name'         => 'Type Name',
                    'type-icon'         => 'Icon',
                    'type-color'        => 'Color',
                    'note'              => 'Address / Location & Notes',
                    'note-placeholder'  => 'Building, floor, field area, or other location details.',
                ],
            ],

            'settings' => [
                'title'  => 'Settings',
                'fields' => [
                    'company'                  => 'Company',
                    'default-custodian'        => 'Default Site Custodian',
                    'default-custodian-help'   => 'Employee automatically assigned as custody receiver when assets are dispatched to this site.',
                    'is-active'                => 'Active',
                    'requires-custodian'       => 'Requires Custodian',
                    'requires-custodian-help'  => 'Field and vehicle sites require a named receiver for each custody handover.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Name',
            'full-name'          => 'Full Name',
            'usage-type'         => 'Usage Type',
            'code'               => 'Code',
            'type'               => 'Type',
            'default-custodian'  => 'Default Custodian',
            'is-active'          => 'Active',
            'requires-custodian' => 'Requires Custodian',
            'assets-count'       => 'Assets',
            'total-cost'         => 'Total Valuation',
            'company'            => 'Company',
            'creator'            => 'Created By',
            'deleted-at'         => 'Archived At',
            'created-at'         => 'Created At',
            'updated-at'         => 'Updated At',
        ],

        'filters' => [
            'usage-type'         => 'Usage Type',
            'parent'             => 'Parent Site',
            'type'               => 'Type',
            'is-active'          => 'Active',
            'requires-custodian' => 'Requires Custodian',
            'company'            => 'Company',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Site updated',
                    'body'  => 'The site has been updated successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Site could not be deleted',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Site Details',
                'entries' => [
                    'name'               => 'Name',
                    'full-name'          => 'Full Name',
                    'parent'             => 'Parent Site',
                    'usage-type'         => 'Usage Type',
                    'code'               => 'Code',
                    'type'               => 'Type',
                    'company'            => 'Company',
                    'is-active'          => 'Active',
                    'requires-custodian' => 'Requires Custodian',
                    'default-custodian'  => 'Default Custodian',
                    'note'               => 'Address / Location & Notes',
                ],
            ],

            'audit' => [
                'title'   => 'Audit',
                'entries' => [
                    'creator'    => 'Created By',
                    'created-at' => 'Created At',
                    'updated-at' => 'Updated At',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'site-assets' => [
            'title'   => 'Site Contents',
            'columns' => [
                'asset-tag'          => 'Asset Tag',
                'asset-name'         => 'Asset',
                'category'           => 'Category',
                'current-site'       => 'Location / Sub-site',
                'serial-no'          => 'Serial No.',
                'receiver'           => 'Received By',
                'condition-out'      => 'Condition Out',
                'condition-in'       => 'Condition In',
                'cost'               => 'Cost',
                'issued-at'          => 'Issued At',
                'expected-return-at' => 'Expected Return',
                'status'             => 'Status',
                'ended-type'         => 'Ended As',
                'issued-by'          => 'Issued By',
            ],
            'filters' => [
                'status' => 'Status',
            ],
            'status' => [
                'open'   => 'On Site',
                'closed' => 'Returned',
            ],
            'tabs' => [
                'all'           => 'All (Including Sub-sites)',
                'direct'        => 'Direct Assets',
                'subsites'      => 'Sub-sites',
                'with-children' => ':site (All)',
                'only'          => ':site (Direct Only)',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'New Asset Site',
                    'notification' => [
                        'title' => 'Site created',
                        'body'  => 'The site has been created successfully.',
                    ],
                ],
            ],
            'views' => [
                'all'             => 'All Sites',
                'asset-locations' => 'Fixed Asset Locations',
                'custody-zones'   => 'Custody Zones',
                'archived'        => 'Archived Sites',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Site created',
                'body'  => 'The site has been created successfully.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Site updated',
                'body'  => 'The site has been updated successfully.',
            ],
        ],
    ],
];
