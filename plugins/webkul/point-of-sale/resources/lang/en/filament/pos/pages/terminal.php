<?php

return [
    'common' => [
        'ok'      => 'Ok',
        'close'   => 'Close',
        'cancel'  => 'Cancel',
        'save'    => 'Save',
        'saving'  => 'Saving…',
        'discard' => 'Discard',
        'clear'   => 'Clear',
        'apply'   => 'Apply',
        'open'    => 'Open',
        'resume'  => 'Resume',
        'confirm' => 'Confirm',
        'back'    => 'Back',
        'print'   => 'Print',
        'new'     => 'New',
        'more'    => '+ :count more',
    ],

    'nav' => [
        'home'      => 'Home',
        'orders'    => 'Orders',
        'registers' => 'Registers',
    ],

    'picker' => [
        'title'       => 'Registers',
        'open'        => 'Open register',
        'resume'      => 'Resume',
        'discard'     => 'Discard session',
        'open-by'     => 'Open by :name',
        'closed'      => 'Closed',
        'opening'     => 'Opening…',
        'back-office' => 'Back office',
        'logout'      => 'Log out',
        'offline'     => 'Connect to the network to open a register.',
        'failed'      => 'Could not reach the server. Try again.',

        'discard-confirm' => [
            'heading' => 'Discard this session?',
            'confirm' => 'Discard',
        ],

        'empty' => [
            'heading'     => 'No point of sale available',
            'description' => 'Create an active point of sale in the back office to start selling.',
        ],
    ],

    'history' => [
        'heading'       => 'Orders',
        'back'          => 'Back',
        'search'        => 'Search by order, receipt, customer or table',
        'empty'         => 'No order matches that search.',
        'select-order'  => 'Select an order to see its details.',
        'walk-in'       => 'Walk-in',
        'invoice'       => 'Invoice',
        'invoiced'      => 'Invoiced',
        'print-invoice' => 'Print invoice',
        'cancel'        => 'Cancel order',
        'print'         => 'Print receipt',
        'details'       => 'Details',
        'refund'        => 'Refund',
        'refund-prompt' => 'Select the product(s) to refund and set the quantity.',
        'to-refund'     => 'To refund',
        'refundable'    => 'Refundable: :qty',
        'taxes'         => 'Taxes',
        'total'         => 'Total',
        'paid'          => 'Paid',
        'lines'         => 'Lines',
        'payments'      => 'Payments',
        'prev'          => 'Previous',
        'next'          => 'Next',
        'page'          => 'Page :page of :total',
        'unreachable'   => 'The server could not be reached. Check the connection and try again.',
        'failed'        => 'The request failed (:status).',

        'invoice-customer' => [
            'heading'        => 'Need customer to invoice',
            'question'       => 'Do you want to open the customer list to select a customer?',
            'select-heading' => 'Select a customer to invoice',
        ],

        'columns' => [
            'date'     => 'Date',
            'receipt'  => 'Receipt',
            'order'    => 'Order',
            'customer' => 'Customer',
            'cashier'  => 'Cashier',
            'tracking' => 'Tracking',
            'table'    => 'Table',
            'total'    => 'Total',
            'status'   => 'Status',
        ],

        'status' => [
            'active'   => 'Active',
            'draft'    => 'Ongoing',
            'paid'     => 'Paid',
            'done'     => 'Posted',
            'invoiced' => 'Invoiced',
            'canceled' => 'Cancelled',
        ],
    ],

    'parked' => [
        'walk-in'         => 'Walk-in',
        'items'           => '{1} :count item|[2,*] :count items',
        'count'           => ':count parked',
        'view-all'        => 'View all',
        'heading'         => 'Parked orders',
        'search'          => 'Search orders by name, reference or product',
        'no-match'        => 'No order matches that search.',
        'parked-ago'      => 'parked :time',
        'discard'         => 'Discard order',
        'discard-confirm' => [
            'heading'     => 'Discard parked order?',
            'description' => 'Its lines will be lost. This cannot be undone.',
        ],

        'empty' => [
            'heading'     => 'Nothing parked',
            'description' => 'Orders you set aside will wait here.',
        ],
    ],

    'opening-control' => [
        'heading'                  => 'Opening Control',
        'cash'                     => 'Opening cash',
        'previous-closing-balance' => 'Previous session closing balance: :amount',
        'note'                     => 'Opening note',
        'placeholder'              => 'Add an opening note…',
        'confirm'                  => 'Open Register',
        'cancel'                   => 'Cancel',
    ],

    'tabs' => [
        'new'      => 'New order',
        'all'      => 'All orders',
        'view-all' => 'View all',
    ],

    'menu' => [
        'label'          => 'Menu',
        'orders'         => 'Orders',
        'cash-in-out'    => 'Cash In / Out',
        'create-product' => 'Create product',
        'back-office'    => 'Back office',
        'close-register' => 'Close register',
        'install'        => 'Install app',
        'theme'          => 'Toggle theme',
        'logout'         => 'Log out',
    ],

    'cash-movement' => [
        'heading' => 'Cash in / out',
        'in'      => 'Cash in',
        'out'     => 'Cash out',
        'amount'  => 'Amount',
        'reason'  => 'Reason',
        'confirm' => 'Record movement',
        'close'   => 'Close',

        'notification' => [
            'title' => 'Cash movement recorded',

            'body' => [
                'in'  => ':amount added to the cash drawer.',
                'out' => ':amount taken out of the cash drawer.',
            ],
        ],
    ],

    'closing' => [
        'copy'                     => 'Copy the expected amount',
        'heading'                  => 'Closing register',
        'expected'                 => 'Expected in drawer',
        'counted'                  => 'Counted',
        'note'                     => 'Closing note',
        'confirm'                  => 'Close register',
        'back'                     => 'Back to the till',
        'method'                   => 'Payment method',
        'total'                    => 'Total',
        'opening'                  => 'Opening',
        'payments'                 => 'Payments in cash',
        'moves'                    => 'Cash in / out',
        'cash-in'                  => 'Cash in :number',
        'cash-out'                 => 'Cash out :number',
        'orders'                   => ':quantity orders',
        'difference'               => 'Difference',
        'count'                    => 'Cash count',
        'clear'                    => 'Clear',
        'discard'                  => 'Discard',
        'daily-sale'               => 'Daily sale',
        'opening-note'             => 'Opening note',
        'authorized-diff'          => 'Maximum allowed difference: :amount',
        'authorized-diff-exceeded' => 'The difference is above the allowed limit. Only a manager can close this register.',
        'unbalanced'               => 'The closing entry is unbalanced by :amount. A manager must select a balancing account to close.',
    ],

    'draft-orders' => [
        'heading'       => 'Error',
        'message'       => 'You cannot close the POS when orders are still in draft.',
        'review-orders' => 'Review Orders',
        'cancel-orders' => 'Cancel Orders',
    ],

    'product-form' => [
        'heading'             => 'New product',
        'name'                => 'Product name',
        'name-placeholder'    => 'e.g. Cheese Burger',
        'barcode'             => 'Barcode',
        'barcode-placeholder' => 'e.g. 1234567890',
        'tracking'            => 'Track inventory',
        'price'               => 'Sales price',
        'taxes'               => 'Sales taxes',
        'tax-included'        => '(= :amount incl. taxes)',
        'category'            => 'PoS category',
        'unsaleable'          => 'Unsaleable',

        'notification' => [
            'title' => 'Product created',
        ],

        'error' => [
            'failed'  => 'Could not create the product (:status)',
            'offline' => 'Creating a product needs a connection.',
        ],
    ],

    'product-info' => [
        'heading'          => 'Product information',
        'inventory'        => 'Inventory',
        'available'        => 'available at this register',
        'negative-warning' => 'Selling is still allowed; stock will go negative and the back office will show the shortfall.',
        'financials'       => 'Financials',
        'price'            => 'Price',
        'cost'             => 'Cost',
        'margin'           => 'Margin',
        'order'            => 'In this order',
        'quantity'         => 'Quantity',
        'total-price'      => 'Total price',
        'total-margin'     => 'Total margin',
        'add'              => 'Add to order',
    ],

    'customers' => [
        'heading'      => 'Select a customer',
        'search'       => 'Search customers',
        'no-match'     => 'No customer matches that search. Only customers loaded at session start are searchable offline.',
        'clear'        => 'Remove customer',
        'badge-new'    => 'new',

        'create' => [
            'label'   => 'New customer',
            'heading' => 'New customer',
            'created' => ':name added and selected.',

            'fields' => [
                'name'  => 'Name',
                'email' => 'Email',
                'phone' => 'Phone',
            ],
        ],
    ],

    'variants' => [
        'heading'     => 'Attribute selection',
        'confirm'     => 'Add',
        'unavailable' => 'This combination does not exist.',
    ],

    'lots' => [
        'heading'        => 'Lot/Serial Number(s) Required',
        'placeholder'    => 'Serial/Lot Number',
        'add'            => 'Add',
        'remove'         => 'Remove number',
        'missing'        => 'Set lot / serial number',
        'none-available' => 'There is no serial/lot number for the selected product, and their creation is not allowed from the Point of Sale app.',

        'warning' => [
            'heading' => 'Some Serial/Lot Numbers are missing',
            'body'    => "You are trying to sell products with serial/lot numbers, but some of them are not set.\nWould you like to proceed anyway?",
            'proceed' => 'Ok',
        ],
    ],

    'notes' => [
        'internal'    => 'Internal note',
        'kitchen'     => 'Kitchen note',
        'heading'     => 'Add Internal Note',
        'empty'       => 'No note models configured.',
        'hint'        => 'Pick a line first, then choose a note.',
        'placeholder' => 'Add a note for this line',
    ],

    'money-details' => [
        'label'            => 'Coins/Notes',
        'opening-heading'  => 'Opening details:',
        'closing-heading'  => 'Closing details:',
        'total'            => 'Total: :total',
        'confirm'          => 'Confirm',
        'close'            => 'Close',
        'increase'         => 'Add one',
        'decrease'         => 'Remove one',
    ],

    'cart' => [
        'discount' => ':percentage% discount',
        'subtotal' => 'Subtotal',
        'tax'      => 'Taxes',
        'rounding' => 'Rounding',
        'total'    => 'Total',
        'remove'   => 'Remove :product',

        'empty' => [
            'description' => 'Scan or tap a product to start',
        ],
    ],

    'catalogue' => [
        'search'          => 'Search products',
        'create-product'  => 'Create product',
        'info'            => 'Product info for :product',
        'info-depleted'   => 'Product info for :product, none available',
    ],

    'numpad' => [
        'qty'       => 'Qty',
        'price'     => 'Price',
        'backspace' => 'Backspace',
        'clear'     => 'C',
    ],

    'payment' => [
        'ship-later'                            => 'Ship Later',
        'ship-later-heading'                    => 'Select the shipping date',
        'ship-later-error-heading'              => 'Incorrect address for shipping',
        'ship-later-no-customer'                => 'Select a customer before shipping this order later.',
        'ship-later-no-address'                 => 'The selected customer needs an address.',
        'select-method'                         => 'Please select a payment method',
        'invoice'                               => 'Invoice',
        'change'                                => 'change',
        'remove'                                => 'Remove :method payment',
        'validate'                              => 'Validate',
        'max-value-title'                       => 'Maximum value reached',
        'max-value-body'                        => "The amount cannot be higher than the due amount if you don't have a cash payment method configured.",
        'customer-required-title'               => 'Customer required',
        'customer-required-body-terminal'       => 'Select a customer before validating this order; this terminal requires one.',
        'customer-required-body-payment-method' => 'Select a customer before validating this order; the selected payment method requires one.',
        'customer-required-body-invoice'        => 'Select a customer before validating this order to generate an invoice.',
        'incomplete-title'                      => 'Payment method required',
        'incomplete-body'                       => 'Select a payment method that covers the total before validating this order.',
    ],

    'floor' => [
        'back'      => 'Floor',
        'table'     => 'Table :table',
        'no-table'  => 'No table',
        'empty'     => 'No tables on this floor yet. Add them in the back office.',
        'no-floors' => 'No floors available. Add a new floor to get started.',
        'seats'     => '{1} :count seat|[2,*] :count seats',
        'orders'    => '{1} :count order|[2,*] :count orders',
        'guests'    => '{1} :count guest|[2,*] :count guests',
    ],

    'guests' => [
        'label'     => 'Guests',
        'heading'   => 'Number of guests',
        'increase'  => 'Add a guest',
        'decrease'  => 'Remove a guest',
        'per-guest' => ':amount per guest',
    ],

    'split' => [
        'label'    => 'Split',
        'heading'  => 'Bill Splitting',
        'hint'     => 'Tap a line to move one unit to the new bill. Tap again to move more.',
        'new-bill' => 'New bill',
        'confirm'  => 'Split Order',
    ],

    'bill' => [
        'label'   => 'Bill',
        'heading' => 'Bill Printing',
    ],

    'takeaway' => [
        'badge'       => 'Takeaway',
        'to-takeaway' => 'Switch to Takeaway',
        'to-dine-in'  => 'Switch to Dine in',
    ],

    'order-name' => [
        'label'       => 'Edit Order Name',
        'heading'     => 'Edit Order Name',
        'placeholder' => 'e.g. 18:45 John 4P',
    ],

    'tip' => [
        'label'  => 'Tip',
        'add'    => 'Add Tip',
        'change' => 'Change Tip',
        'remove' => 'Remove tip',
    ],

    'global-discount' => [
        'label'   => 'Discount',
        'heading' => 'Discount Percentage',
        'apply'   => 'Apply',
        'hint'    => 'Replaces any discount already on the order.',
    ],

    'booking' => [
        'book'    => 'Book table',
        'release' => 'Release table',
    ],

    'transfer' => [
        'label'        => 'Transfer / Merge',
        'prompt'       => 'Select a table to transfer :order',
        'has-payments' => 'This order has payments. Remove them before merging it into another table.',
    ],

    'table-selector' => [
        'label'       => 'Table',
        'heading'     => 'Table Selector',
        'hint'        => 'Enter a table number, or a name for an order without a table.',
        'placeholder' => 'Table number or name',
        'jump'        => 'Jump',
    ],

    'session-closed' => [
        'heading' => 'Register closed',
        'body'    => 'This register was closed from another window or device. Open orders on this device were not sent; reopen the register to continue.',
        'back'    => 'Back to registers',
    ],

    'floor-plan' => [
        'edit'                 => 'Edit plan',
        'switch-view'          => 'Switch floor view',
        'view-map'             => 'Map view',
        'view-grid'            => 'Grid view',
        'floor-name'           => 'Floor name',
        'background'           => 'Floor background',
        'no-colour'            => 'No colour',
        'add-table'            => 'Add table',
        'add-floor'            => 'Add floor',
        'delete-floor'         => 'Delete floor',
        'confirm-delete-floor' => 'Confirm delete',
        'table-number'         => 'Table',
        'fewer-seats'          => 'Fewer seats',
        'more-seats'           => 'More seats',
        'make-square'          => 'Make square',
        'make-round'           => 'Make round',
        'duplicate'            => 'Duplicate',
        'delete-table'         => 'Delete table',
        'hint'                 => 'Drag tables to move them, drag the corner handle to resize, tap a table to edit it.',
        'new-floor'            => 'Floor :number',
        'failed'               => 'Could not save the floor plan (:status).',
        'add-image'            => 'Add image',
        'change-image'         => 'Change image',
        'remove-image'         => 'Remove image',
        'zoom-in'              => 'Zoom in',
        'zoom-out'             => 'Zoom out',
        'fit'                  => 'Fit to screen',
        'unreachable'          => 'The server could not be reached. Check the connection and try again.',

        'colours' => [
            'red'        => 'Red',
            'orange'     => 'Orange',
            'yellow'     => 'Yellow',
            'green'      => 'Green',
            'teal'       => 'Teal',
            'blue'       => 'Blue',
            'violet'     => 'Violet',
            'pink'       => 'Pink',
            'stone'      => 'Stone',
            'white'      => 'White',
            'light-grey' => 'Light grey',
            'cream'      => 'Cream',
            'mint'       => 'Mint',
            'sky'        => 'Sky',
            'lavender'   => 'Lavender',
            'blush'      => 'Blush',
            'warm-grey'  => 'Warm grey',
        ],
    ],

    'receipt' => [
        'phone'      => 'Tel:',
        'served-by'  => 'Served by :cashier',
        'lot-number' => 'Lot Number :numbers',
        'untaxed'    => 'Untaxed amount',
        'rounding'   => 'Rounding',
        'total'      => 'TOTAL',
        'change'     => 'Change',
        'order'      => 'Order :order',
        'new-order'  => 'New order',
        'guests'     => '{1} :count guest|[2,*] :count guests',
    ],

    'install' => [
        'installed'   => 'This register is already installed as an app on this device.',
        'unavailable' => 'This browser cannot install the register. Chrome or Edge over HTTPS is required.',
        'label'       => 'Install App',
    ],

    'scanner' => [
        'unsupported'   => 'This browser cannot scan with the camera. Use an attached barcode scanner instead.',
        'hardware-hint' => 'An attached barcode scanner works everywhere in the till without opening this.',
        'heading'       => 'Scan a barcode',
        'start'         => 'Scan with the camera',
        'stop'          => 'Stop',
    ],

    'drafts' => [
        'not-shared' => 'This order is not shared with the other tills',
        'rejected'   => 'The server refused this order.',
    ],

    'kitchen' => [
        'order'        => 'Order',
        'new'          => 'New',
        'cancelled'    => 'Cancelled',
        'note-changed' => 'Note changed',
        'dine-in'      => 'Dine in',
        'takeaway'     => 'Take out',
        'to-takeaway'  => 'Dine in → Take out',
        'to-dine-in'   => 'Take out → Dine in',
        'by'           => 'By :name',
        'table'        => 'Table :table',
        'order-note'   => 'Order note',
        'offline'      => 'Sending to the kitchen needs a connection.',
        'nothing'      => 'Nothing new to send to the kitchen.',
    ],

    'rejected' => [
        'settled-heading' => 'Paid at another till',
        'failed-heading'  => 'This order was not saved',
        'order'           => 'Order :order',
        'table'           => 'Table :table',
        'settled-body'    => ':order was already paid at another till. This sale was not recorded.',
        'give-back'       => 'Give back :amount to the customer.',
        'failed-body'     => ':order could not be saved on the server.',
        'returned'        => 'Money returned',
        'dismiss'         => 'Dismiss',
    ],

    'offline' => [
        'banner'              => 'Offline — sales continue and sync when the connection returns',
        'waiting'             => ':count order(s) waiting to sync',
        'rejected'            => ':count order(s) rejected by the server',
        'storage-unavailable' => 'Local storage unavailable — reload before taking more orders',
        'retry'               => 'Retry',
        'lost-heading'        => 'Connection Lost',
        'lost-body'           => 'Until the connection is reestablished, the point of sale will operate with limited functionality.',
        'continue'            => 'Continue with limited functionality',
    ],

    'actions' => [
        'customer' => 'Customer',
        'note'     => 'Note',
        'payment'  => 'Payment',
        'heading'  => 'Actions',
        'label'    => 'Actions',

        'cancel-order' => [
            'label'   => 'Cancel order',
            'confirm' => 'Confirm',
            'hint'    => 'Its lines will be lost. This cannot be undone.',
            'failed'  => 'Could not cancel the order (:status).',
            'offline' => 'Cancelling this order needs a connection.',
        ],
    ],

    'price-lists' => [
        'label'   => 'Pricelist',
        'heading' => 'Select the pricelist',
        'default' => 'Default Price',
    ],

    'notification' => [
        'success' => [
            'title' => 'Order completed',
            'body'  => 'Order :order has been settled.',
        ],

        'queued' => [
            'title' => 'Order queued offline',
            'body'  => ':count order(s) will sync when the connection returns.',
        ],
    ],
];
