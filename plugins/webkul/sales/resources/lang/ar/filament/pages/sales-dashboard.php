<?php

return [
    'navigation' => [
        'title' => 'المبيعات',
    ],

    'navigation-group' => [
        'title' => 'لوحة التحكم',
    ],

    'filters-form' => [
        'start-date'     => 'تاريخ البدء',
        'end-date'       => 'تاريخ الانتهاء',
        'salesperson'    => 'مندوب المبيعات',
        'country'        => 'الدولة',
        'product'        => 'المنتج',
        'customer'       => 'العميل',
        'category'       => 'الفئة',
        'salesteam'      => 'فريق المبيعات',
    ],

    'widgets' => [
        'stats-overview' => [
            'heading'          => 'نظرة عامة على المبيعات',
            'quotation'        => 'عرض السعر',
            'order'            => 'الطلب',
            'draft'            => 'عرض سعر مسودة',
            'cancel'           => 'إلغاء عرض السعر',
            'total-revenue'    => 'إجمالي الإيرادات',
            'avg-revenue'      => 'متوسط الإيرادات',
            'fully-invoiced'   => 'مفوتر بالكامل',
            'archived'         => 'مؤرشف',
            'no-change'        => 'بدون تغيير',
            'increase'         => 'زيادة',
            'decrease'         => 'نقصان',

            'descriptions' => [
                'quotation'     => 'عروض الأسعار المرسلة إلى العملاء',
                'order'         => 'الطلبات المؤكدة من العملاء',
                'draft'         => 'عروض أسعار في حالة مسودة',
                'cancel'        => 'عرض السعر الملغى من قبل العملاء',
                'total-revenue' => 'إجمالي الإيرادات من الطلبات',
                'avg-revenue'   => 'متوسط الإيرادات من الطلبات',
                'fully-invoiced'=> 'الطلبات المفوترة بالكامل',
                'archived'      => 'الطلبات المؤرشفة',
            ],
        ],

        'sales-chart' => [
            'heading'          => 'مخطط المبيعات',
            'confirmed-orders' => 'الطلبات المؤكدة',
            'draft-orders'     => 'الطلبات المسودة',
            'sent-orders'      => 'عروض الأسعار المرسلة',
            'cancelled-orders' => 'الطلبات الملغاة',
        ],

        'revenue-chart' => [
            'heading' => 'مخطط الإيرادات',
            'label'   => 'الإيرادات',
        ],

        'yearly-comparison' => [
            'heading' => 'مقارنة المبيعات السنوية',
            'label'   => 'المبيعات',
        ],

        'top-categories' => [
            'heading' => 'أفضل الفئات',
            'column'  => [
                'category'              => 'الفئة',
                'category_full_name'    => 'الاسم الكامل',
                'product_count'         => 'عدد المنتجات',
            ],
        ],

        'top-customers' => [
            'heading' => 'أفضل العملاء',
            'column'  => [
                'customer'      => 'العميل',
                'total_orders'  => 'إجمالي الطلبات',
                'total_revenue' => 'إجمالي الإيرادات',
            ],
        ],

        'top-products' => [
            'heading' => 'أفضل المنتجات',
            'column'  => [
                'product'       => 'المنتج',
                'qty_sold'      => 'الكمية المباعة',
                'total_revenue' => 'إجمالي الإيرادات',
            ],
        ],

        'top-sales-teams' => [
            'heading' => 'أفضل فرق المبيعات',
            'column'  => [
                'sales_team'    => 'فريق المبيعات',
                'total_orders'  => 'إجمالي الطلبات',
                'total_revenue' => 'الإيرادات',
            ],
        ],

        'top-sales-orders' => [
            'heading' => 'أفضل طلبات المبيعات',
            'column'  => [
                'order'         => 'الطلب',
                'customer'      => 'العميل',
                'order_date'    => 'تاريخ الطلب',
                'total_amount'  => 'المبلغ الإجمالي',
            ],
        ],

        'top-sales-countries' => [
            'heading' => 'أفضل دول المبيعات',
            'column'  => [
                'country'        => 'الدولة',
                'total_products' => 'إجمالي المنتجات',
                'total_revenue'  => 'إجمالي الإيرادات',
            ],
        ],
    ],
];
