<?php

return [
    'form' => [
        'fields' => [
            'accrual-amount'              => 'مبلغ الاستحقاق',
            'accrual-value-type'          => 'نوع قيمة الاستحقاق',
            'accrual-frequency'           => 'تكرار الاستحقاق',
            'accrual-day'                 => 'يوم الاستحقاق',
            'day-of-month'                => 'يوم الشهر',
            'first-day-of-month'          => 'اليوم الأول من الشهر',
            'second-day-of-month'         => 'اليوم الثاني من الشهر',
            'first-period-month'          => 'شهر الفترة الأولى',
            'first-period-day'            => 'يوم الفترة الأولى',
            'second-period-month'         => 'شهر الفترة الثانية',
            'second-period-day'           => 'يوم الفترة الثانية',
            'first-period-year'           => 'سنة الفترة الأولى',
            'cap-accrued-time'            => 'حد الوقت المستحق',
            'days'                        => 'الأيام',
            'start-count'                 => 'عدد البداية',
            'start-type'                  => 'نوع البداية',
            'action-with-unused-accruals' => 'الإجراء مع الاستحقاقات غير المستخدمة',
            'milestone-cap'               => 'حد المرحلة',
            'maximum-leave-yearly'        => 'الحد الأقصى للإجازة السنوية',
            'accrual-validity'            => 'صلاحية الاستحقاق',
            'accrual-validity-count'      => 'عدد صلاحية الاستحقاق',
            'accrual-validity-type'       => 'نوع صلاحية الاستحقاق',
            'advanced-accrual-settings'   => 'إعدادات الاستحقاق المتقدمة',
            'after-allocation-start'      => 'بعد تاريخ بدء التخصيص',
        ],
    ],

    'table' => [
        'columns' => [
            'accrual-amount'     => 'مبلغ الاستحقاق',
            'accrual-value-type' => 'نوع قيمة الاستحقاق',
            'frequency'          => 'التكرار',
            'maximum-leave-days' => 'الحد الأقصى لأيام الإجازة',
        ],

        'groups' => [
            'accrual-amount'       => 'مبلغ الاستحقاق',
            'accrual-value-type'   => 'نوع قيمة الاستحقاق',
            'frequency'            => 'التكرار',
            'maximum-leave-days'   => 'الحد الأقصى لأيام الإجازة',
        ],

        'filters' => [
            'accrual-frequency'           => 'تكرار الاستحقاق',
            'start-type'                  => 'نوع البداية',
            'cap-accrued-time'            => 'حد الوقت المستحق',
            'action-with-unused-accruals' => 'الإجراء مع الاستحقاقات غير المستخدمة',
            'accrual-amount'              => 'مبلغ الاستحقاق',
            'accrual-frequency'           => 'تكرار الاستحقاق',
            'start-type'                  => 'نوع البداية',
            'created-at'                  => 'تاريخ الإنشاء',
            'updated-at'                  => 'تاريخ التحديث',
        ],

        'header-actions' => [
            'created' => [
                'title' => 'خطة استحقاق إجازة جديدة',

                'notification' => [
                    'title' => 'تم إنشاء خطة استحقاق الإجازة',
                    'body'  => 'تم إنشاء خطة استحقاق الإجازة بنجاح.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث خطة استحقاق الإجازة',
                    'body'  => 'تم تحديث خطة استحقاق الإجازة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف خطة استحقاق الإجازة',
                    'body'  => 'تم حذف خطة استحقاق الإجازة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف خطط استحقاق الإجازة',
                    'body'  => 'تم حذف خطط استحقاق الإجازة بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'accrual-amount'              => 'مبلغ الاستحقاق',
            'accrual-value-type'          => 'نوع قيمة الاستحقاق',
            'accrual-frequency'           => 'تكرار الاستحقاق',
            'accrual-day'                 => 'يوم الاستحقاق',
            'day-of-month'                => 'يوم الشهر',
            'first-day-of-month'          => 'اليوم الأول من الشهر',
            'second-day-of-month'         => 'اليوم الثاني من الشهر',
            'first-period-month'          => 'شهر الفترة الأولى',
            'first-period-day'            => 'يوم الفترة الأولى',
            'second-period-month'         => 'شهر الفترة الثانية',
            'second-period-day'           => 'يوم الفترة الثانية',
            'first-period-year'           => 'سنة الفترة الأولى',
            'cap-accrued-time'            => 'حد الوقت المستحق',
            'days'                        => 'الأيام',
            'start-count'                 => 'عدد البداية',
            'start-type'                  => 'نوع البداية',
            'action-with-unused-accruals' => 'الإجراء مع الاستحقاقات غير المستخدمة',
            'milestone-cap'               => 'حد المرحلة',
            'maximum-leave-yearly'        => 'الحد الأقصى للإجازة السنوية',
            'accrual-validity'            => 'صلاحية الاستحقاق',
            'accrual-validity-count'      => 'عدد صلاحية الاستحقاق',
            'accrual-validity-type'       => 'نوع صلاحية الاستحقاق',
            'advanced-accrual-settings'   => 'إعدادات الاستحقاق المتقدمة',
            'after-allocation-start'      => 'بعد تاريخ بدء التخصيص',
        ],
    ],
];
