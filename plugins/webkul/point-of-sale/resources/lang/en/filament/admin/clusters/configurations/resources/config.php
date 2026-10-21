<?php

return [
    'navigation' => [
        'title' => 'Point of Sale',
        'group' => 'Point of Sale',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Point of Sale',
                    'name-placeholder' => 'e.g. NYC Shop',
                    'code'             => 'Short Code',
                    'code-helper-text' => 'Used as the prefix for order and session numbers.',
                    'is-active'        => 'Active',
                ],
            ],

            'configurations' => [
                'title' => 'Configurations',

                'tabs' => [
                    'restaurant' => [
                        'title' => 'Restaurant Mode',

                        'fields' => [
                            'is-restaurant'                 => 'Is a Bar/Restaurant',
                            'is-restaurant-helper-text'     => 'Enable table management, split bills and preparation tickets.',
                            'enable-split-bill'             => 'Allow Bill Splitting',
                            'enable-split-bill-helper-text' => 'Split total or order lines.',
                            'enable-print-bill'             => 'Early Receipt Printing',
                            'enable-print-bill-helper-text' => 'Allow to print receipt before payment.',
                            'enable-takeaway'               => 'Eat in / Take out',
                            'enable-takeaway-helper-text'   => 'Adjust the tax rate based on whether customers are dining in or opting for takeout.',
                            'takeaway-fiscal-position'      => 'Alternative Fiscal Position',
                            'floors'                        => 'Floors & Tables Map',
                            'floors-helper-text'            => 'Design floors and assign orders to tables.',
                        ],
                    ],

                    'payment' => [
                        'title' => 'Payment',

                        'fields' => [
                            'payment-methods'                        => 'Payment Methods',
                            'enable-maximum-difference'              => 'Set Maximum Difference',
                            'enable-maximum-difference-helper-text'  => 'Set a maximum difference allowed between the expected and counted money during the closing of the session.',
                            'amount-authorized-diff'                 => 'Maximum Difference',
                            'enable-cash-rounding'                   => 'Cash Rounding',
                            'enable-cash-rounding-helper-text'       => 'Define the smallest coinage of the currency used to pay by cash.',
                            'cash-rounding'                          => 'Rounding Method',
                            'enable-only-round-cash-method'          => 'Only apply rounding on cash',
                            'enable-tip'                             => 'Tips',
                            'enable-tip-helper-text'                 => 'Accept customer tips or convert their change to a tip.',
                            'tip-product'                            => 'Tip Product',
                        ],
                    ],

                    'interface' => [
                        'title' => 'PoS Interface',

                        'fields' => [
                            'enable-customer-required'              => 'Customer Required',
                            'show-product-images'                   => 'Show product images',
                            'show-category-images'                  => 'Show category images',
                            'limited-products-amount'               => 'Products Loaded',
                            'limited-products-amount-helper-text'   => 'Number of products loaded into the terminal when a session opens.',
                        ],
                    ],

                    'products' => [
                        'title' => 'Product & PoS categories',

                        'fields' => [
                            'show-margins-and-costs'             => 'Show margins & costs',
                            'show-margins-and-costs-helper-text' => 'Show margins & costs on product information.',
                            'limit-categories'                   => 'Restrict Categories',
                            'limit-categories-helper-text'       => 'Pick which product PoS categories are available.',
                            'locked-by-session'                  => 'Locked while a session is open',
                            'categories'                         => 'Available PoS Product Categories',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Accounting',

                        'fields' => [
                            'journal'                                  => 'Orders Journal',
                            'journal-helper-text'                      => 'Journal used for the session closing entry.',
                            'invoice-journal'                          => 'Invoices Journal',
                            'is-closing-entry-by-product'              => 'Closing Entry by product',
                            'is-closing-entry-by-product-helper-text'  => 'Display the breakdown of sales lines by product in the automatically generated closing entry.',
                            'enable-fiscal-position'                   => 'Flexible Taxes',
                            'enable-fiscal-position-helper-text'       => 'Use fiscal positions to get different taxes by order.',
                            'fiscal-position'                          => 'Default Fiscal Position',
                            'fiscal-positions'                         => 'Fiscal Positions',
                            'receivable-account'                       => 'Intermediary Account',
                            'receivable-account-helper-text'           => 'Leave empty to use the default receivable account from the Point of Sale settings.',
                            'cash-movement-account'                    => 'Cash In/Out Account',
                            'balancing-account'                        => 'Balancing Account',
                            'enable-cogs'                              => 'Cost of Goods Sold',
                            'enable-cogs-helper-text'                  => 'Post the cost of sold products at session closing.',
                            'cogs-journal'                             => 'Cost of Goods Sold Journal',
                            'stock-output-account'                     => 'Stock Output Account',
                        ],
                    ],

                    'pricing' => [
                        'title' => 'Pricing',

                        'fields' => [
                            'tax-display'                            => 'Product Prices',
                            'tax-display-helper-text'                => 'Product prices shown on the terminal and on receipts.',
                            'enable-price-control'                   => 'Price Control',
                            'enable-price-control-helper-text'       => 'Restrict price modification to managers.',
                            'enable-price-list'                      => 'Flexible Pricelists',
                            'enable-price-list-helper-text'          => 'Set multiple prices per product, automated discounts, etc.',
                            'price-list'                             => 'Default Pricelist',
                            'price-list-helper-text'                 => 'Applied to every order on this register. Cashiers can switch lists only when flexible pricelists are on.',
                            'price-lists'                            => 'Available Pricelists',
                            'enable-line-discount'                   => 'Line Discounts',
                            'enable-line-discount-helper-text'       => 'Allow cashiers to set a discount per line.',
                            'enable-global-discount'                 => 'Global Discounts',
                            'enable-global-discount-helper-text'     => 'Adds a button to set a global discount.',
                            'discount-product'                       => 'Discount Product',
                            'global-discount-percentage'             => 'Discount %',
                            'global-discount-percentage-helper-text' => 'Default percentage offered when the cashier applies a global discount.',
                        ],
                    ],

                    'receipts' => [
                        'title' => 'Bills & Receipts',

                        'fields' => [
                            'enable-receipt-print'                   => 'Receipt Printing',
                            'enable-receipt-print-helper-text'       => 'Show a Print button on the receipt screen after payment.',
                            'enable-receipt-auto-print'              => 'Automatic Receipt Printing',
                            'enable-receipt-auto-print-helper-text'  => 'Print receipts automatically once the payment is registered.',
                            'receipt-header'                         => 'Receipt Header',
                            'receipt-footer'                         => 'Receipt Footer',
                            'bills'                                  => 'Coins/Bills',
                            'bills-helper-text'                      => 'Denominations offered when counting the cash at opening and closing.',
                        ],
                    ],

                    'preparation' => [
                        'title' => 'Preparation',

                        'fields' => [
                            'printers'             => 'Preparation Printers',
                            'printers-helper-text' => 'Print orders at the kitchen, at the bar, etc.',
                        ],
                    ],

                    'inventory' => [
                        'title' => 'Inventory',

                        'fields' => [
                            'operation-type'                => 'Operation Type',
                            'operation-type-helper-text'    => 'Used to record product pickings. Products are consumed from its default source location.',
                            'return-operation-type'         => 'Return Operation Type',
                            'warehouse'                     => 'Warehouse',
                            'enable-ship-later'             => 'Allow Ship Later',
                            'enable-ship-later-helper-text' => 'Sell products and deliver them later.',
                            'ship-later-route'              => 'Specific Route',
                            'picking-policy'                => 'Shipping Policy',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'           => 'Point of Sale',
                    'code'           => 'Short Code',
                    'company-name'   => 'Company',
                    'warehouse-name' => 'Warehouse',
                    'journal-name'   => 'Orders Journal',
                    'is-restaurant'  => 'Is a Bar/Restaurant',
                    'is-active'      => 'Active',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Name',
            'closing'        => 'Closing',
            'balance'        => 'Balance',
            'code'           => 'Code',
            'warehouse'      => 'Warehouse',
            'operation-type' => 'Operation Type',
            'journal'        => 'Sales Journal',
            'is-restaurant'  => 'Restaurant',
            'is-active'      => 'Active',
            'company'        => 'Company',
        ],

        'groups' => [
            'warehouse' => 'Warehouse',
            'company'   => 'Company',
        ],

        'record-actions' => [
            'restore' => [
                'notification' => [
                    'success' => [
                        'title' => 'Point of sale restored',
                        'body'  => 'The point of sale has been restored.',
                    ],
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Point of sale deleted',
                        'body'  => 'The point of sale has been deleted.',
                    ],
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Point of sale deleted permanently',
                        'body'  => 'The point of sale has been deleted permanently.',
                    ],

                    'error' => [
                        'title' => 'Point of sale could not be deleted',
                        'body'  => 'The point of sale cannot be deleted permanently because it has sessions or orders. Archive it instead.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Points of sale deleted permanently',
                        'body'  => 'The selected points of sale have been deleted permanently.',
                    ],

                    'error' => [
                        'title' => 'Points of sale could not be deleted',
                        'body'  => 'One or more points of sale cannot be deleted permanently because they have sessions or orders. Archive them instead.',
                    ],
                ],
            ],
        ],
    ],
];
