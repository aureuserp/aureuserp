<?php

return [
    'title' => 'Asset Settings',

    'navigation' => [
        'title' => 'Assets & Tagging',
        'group' => 'Assets',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'       => 'General Asset Configuration',
                'description' => 'Manage default asset identification, numbering sequences, and operational parameters.',
            ],
            'warehouses_and_operation' => [
                'title'       => 'Warehouses & Operational Models',
                'description' => 'Configure default storage warehouses and manage fixed site-level asset deployments.',
            ],
            'custody_and_duration' => [
                'title'       => 'Custody Rules & Durations',
                'description' => 'Define custody duration periods, expected return dates, and assignment restrictions.',
            ],
            'governance_and_attachments' => [
                'title'       => 'Documentation & Termination Governance',
                'description' => 'Enforce attachment requirements for custody transitions and govern asset disposal workflows.',
            ],
            'consumables_rules' => [
                'title'       => 'Consumables Issuance & Quantities',
                'description' => 'Control batch issuance, automatic recipient selection, default quantities, and site or attachment requirements for consumable products.',
            ],
        ],
        'fields' => [
            'tag-prefix'                              => 'Asset Tag Prefix',
            'auto-generate-tag'                       => 'Auto-generate Asset Tag',
            'tag-padding'                             => 'Tag Number Padding',
            'default-condition'                       => 'Default Asset Condition',
            'require-serial-by-default'               => 'Require Serial Number by Default',
            'default-warehouse'                       => 'Default Warehouse',
            'enable-site-assets'                      => 'Enable Fixed Site Assets',
            'require-site-on-custody'                 => 'Require Site on Custody Issue',
            'default-custody-duration-days'           => 'Default Custody Duration (Days)',
            'require-expected-return-date'            => 'Require Expected Return Date',
            'prevent-duplicate-product-custody'       => 'Prevent Duplicate Product Custody',
            'require-attachments-on-custody'          => 'Require Attachments on Custody Handover',
            'enable-consumables-batch-issue'          => 'Enable Batch Issuance for Consumables',
            'auto-fill-consumable-employees-by-site'  => 'Auto-fill Recipients from Site',
            'default-consumable-issue-quantity'       => 'Default Issued Quantity per Recipient',
            'require-site-on-consumable-issue'        => 'Require Site on Consumable Issue',
            'require-attachments-on-consumable-issue' => 'Require Attachments on Consumable Issue',
            'require-attachments-on-damage'           => 'Require Attachments on Damaged Return',
            'require-attachments-on-termination'      => 'Require Attachments on Termination',
            'restrict-termination-to-managers'        => 'Restrict Termination to Managers',
            'termination-approval-cost-threshold'     => 'Termination Cost Approval Threshold',
        ],
        'helpers' => [
            'tag-prefix'                              => 'Prefix used when auto-generating asset identification tags (e.g. AST-).',
            'tag-prefix-locked'                       => 'The tag prefix is locked because assets have already been registered in the system.',
            'auto-generate-tag'                       => 'Automatically assign sequential tags to newly created company assets.',
            'tag-padding'                             => 'Number of digits for tag sequential number (3 to 8). Preview: :preview',
            'default-condition'                       => 'The initial condition assigned to newly registered assets.',
            'require-serial-by-default'               => 'Prompt for and enforce mandatory serial numbers during single unit registration.',
            'default-warehouse'                       => 'Default company warehouse assigned to available assets if none is specified.',
            'manage-warehouses-link'                  => 'Manage company warehouses',
            'enable-site-assets'                      => 'Allow assigning assets directly to fixed site locations without an individual employee.',
            'manage-sites-link'                       => 'Manage company sites',
            'require-site-on-custody'                 => 'Enforce selecting an operational site whenever issuing an asset to an employee.',
            'default-custody-duration-days'           => 'Default number of days added to issue date to populate expected return date.',
            'require-expected-return-date'            => 'Mandate an expected return date on all employee custody handovers.',
            'prevent-duplicate-product-custody'       => 'Prevent assigning multiple assets of the same product model to the same employee simultaneously.',
            'require-attachments-on-custody'          => 'Enforce uploading handover receipts or delivery notes during issue and transfer movements.',
            'enable-consumables-batch-issue'          => 'Allow issuing a consumable batch to multiple recipients in a single movement.',
            'auto-fill-consumable-employees-by-site'  => 'Populate the recipient list with active employees of the selected site automatically.',
            'default-consumable-issue-quantity'       => 'Quantity deducted per recipient when issuing a consumable batch, from 1 to 10000.',
            'require-site-on-consumable-issue'        => 'Enforce selecting a custody site when issuing consumables, independently of tracked asset rules.',
            'require-attachments-on-consumable-issue' => 'Enforce uploading handover evidence when issuing consumables, independently of tracked asset rules.',
            'require-attachments-on-damage'           => 'Enforce uploading photo evidence when returning an asset in poor or damaged condition.',
            'require-attachments-on-termination'      => 'Enforce uploading official approval or disposal documentation when scrapping or marking an asset lost.',
            'restrict-termination-to-managers'        => 'Require Group or Global manager permissions to scrap or declare assets lost.',
            'termination-approval-cost-threshold'     => 'Assets with acquisition cost below this threshold can be scrapped by officers without manager approval. Leave empty for all amounts.',
        ],
    ],

    'warnings' => [
        'site-assets-held' => 'Cannot disable site assets while active assets are currently deployed to fixed sites.',
    ],
];
