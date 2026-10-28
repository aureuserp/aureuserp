<?php

return [
    'title' => 'إعدادات الأصول',

    'navigation' => [
        'title' => 'إدارة الأصول والترميز',
        'group' => 'الأصول',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'       => 'التهيئة العامة للأصول',
                'description' => 'إدارة الترقيم التلقائي للأصول، البادئات، والمعايير التشغيلية الأساسية.',
            ],
            'warehouses_and_operation' => [
                'title'       => 'المستودعات وأنماط التشغيل',
                'description' => 'تحديد المستودعات الافتراضية للتخزين وإدارة تخصيص الأصول للمواقع الثابتة.',
            ],
            'custody_and_duration' => [
                'title'       => 'ضوابط العهد ومدد الحيازة',
                'description' => 'تحديد المدد الافتراضية للعهد، إلزامية تواريخ الإرجاع، وضوابط منع الازدواجية.',
            ],
            'governance_and_attachments' => [
                'title'       => 'التوثيق وحوكمة الإتلاف والفقدان',
                'description' => 'إلزامية المستندات والمرفقات لحركات العهدة وحوكمة شطب الأصول والتالف.',
            ],
        ],
        'fields' => [
            'tag-prefix'                          => 'بادئة وسم الأصل',
            'auto-generate-tag'                   => 'توليد وسم الأصل تلقائياً',
            'tag-padding'                         => 'عدد خانات الرقم التسلسلي',
            'default-condition'                   => 'الحالة الافتراضية للأصل',
            'require-serial-by-default'           => 'إلزامية الرقم التسلسلي افتراضياً',
            'default-warehouse'                   => 'المستودع الافتراضي',
            'enable-site-assets'                  => 'تفعيل أصول المواقع الثابتة',
            'require-site-on-custody'             => 'إلزامية تحديد الموقع عند تسليم العهدة',
            'default-custody-duration-days'       => 'مدة العهدة الافتراضية (بالأيام)',
            'require-expected-return-date'        => 'إلزامية تاريخ الإرجاع المتوقع',
            'prevent-duplicate-product-custody'   => 'منع تكرار صنف الأصل للموظف',
            'require-attachments-on-custody'      => 'إلزامية المرفقات عند تسليم العهدة',
            'require-attachments-on-damage'       => 'إلزامية المرفقات عند الإرجاع بحالة تالفة',
            'require-attachments-on-termination'  => 'إلزامية المرفقات عند الإتلاف أو الفقدان',
            'restrict-termination-to-managers'    => 'قصر شطب الأصل على المديرين',
            'termination-approval-cost-threshold' => 'الحد المالي لاعتماد المدير عند الشطب',
        ],
        'helpers' => [
            'tag-prefix'                          => 'البادئة المستخدمة في توليد وسوم الأصول التلقائية (مثل AST-).',
            'tag-prefix-locked'                   => 'بادئة الوسم مقفلة لأنه تم تسجيل أصول بالفعل في النظام.',
            'auto-generate-tag'                   => 'تعيين وسوم تسلسلية تلقائية للأصول الجديدة في الشركة.',
            'tag-padding'                         => 'عدد خانات الرقم التسلسلي للوسم (من 3 إلى 8). معاينة: :preview',
            'default-condition'                   => 'الحالة التشغيلية الأولية الممنوحة للأصول الجديدة.',
            'require-serial-by-default'           => 'إلزام المستخدمين بإدخال الرقم التسلسلي عند تسجيل أصل منفرد.',
            'default-warehouse'                   => 'المستودع الافتراضي الذي يتم توجيه الأصول المتاحة إليه.',
            'manage-warehouses-link'              => 'إدارة مستودعات الشركة',
            'enable-site-assets'                  => 'السماح بتخصيص الأصول مباشرة لمواقع ثابتة بدون موظف مستلم.',
            'manage-sites-link'                   => 'إدارة مواقع التشغيل',
            'require-site-on-custody'             => 'إلزام تحديد موقع تشغيلي عند تسليم العهدة لموظف.',
            'default-custody-duration-days'       => 'عدد الأيام المضاف لتاريخ التسليم لاحتساب تاريخ الإرجاع المتوقع تلقائياً.',
            'require-expected-return-date'        => 'فرض تحديد تاريخ الإرجاع المتوقع عند صرف أي عهدة لموظف.',
            'prevent-duplicate-product-custody'   => 'منع صرف أكثر من قطعة من نفس صنف الأصل للموظف في نفس الوقت.',
            'require-attachments-on-custody'      => 'إلزام إرفاق سندات الاستلام ومحاضر التسليم في حركات الصرف والنقل.',
            'require-attachments-on-damage'       => 'إلزام إرفاق صور ومستندات توثيق الضرر عند إرجاع الأصل بحالة سيئة أو تالفة.',
            'require-attachments-on-termination'  => 'إلزام إرفاق المحاضر والتقارير الرسمية عند شطب الأصل أو تسجيل فقدانه.',
            'restrict-termination-to-managers'    => 'اشتراط صلاحيات مدير (نطاق مجموعة أو عام) لشطب الأصل أو تسجيل فقدانه.',
            'termination-approval-cost-threshold' => 'الأصول التي تكلفتها أقل من هذا الحد يمكن للمسؤول شطبها دون اعتماد مدير. اتركه فارغاً لكافة المبالغ.',
        ],
    ],

    'warnings' => [
        'site-assets-held' => 'لا يمكن تعطيل أصول المواقع بينما توجد أصول نشطة مخصصة حالياً لمواقع ثابتة.',
    ],
];
