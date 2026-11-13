<?php

return [
    'layouts' => [
        'list'   => 'قائمة',
        'kanban' => 'كانبان',
        'graph'  => 'رسم بياني',
    ],

    'switcher' => [
        'label' => 'تخطيط العرض',
    ],

    'card' => [
        'title'        => 'العنوان',
        'priority'     => 'الأولوية',
        'quick-create' => 'إضافة مهمة',
        'assignees'    => 'المكلفون',
        'schedule'     => 'جدولة',
        'view'         => 'عرض',
        'edit'         => 'تعديل',
        'delete'       => 'حذف',
        'share'        => 'نسخ الرابط',
        'shared'       => 'تم نسخ الرابط',
        'tags-more'    => ':count أخرى',
    ],

    'column' => [
        'add'               => 'إضافة مرحلة',
        'add-project'       => 'إضافة مشروع',
        'edit-project'      => 'تعديل المشروع',
        'delete-project'    => 'حذف المشروع',
        'project-settings'  => 'إعدادات المشروع',
        'private'           => 'خاصة',
        'none'              => 'لا شيء',
        'name'              => 'الاسم',
        'edit'              => 'تعديل المرحلة',
        'delete'            => 'حذف المرحلة',
        'settings'          => 'إعدادات المرحلة',
        'empty-heading'     => 'لا توجد مهام',
        'empty-description' => 'اسحب مهمة هنا أو استخدم + لإنشاء واحدة.',
    ],

    'group' => [
        'project'   => 'المشروع',
        'stage'     => 'المرحلة',
        'partner'   => 'العميل',
        'company'   => 'الشركة',
        'milestone' => 'المعلم',
        'state'     => 'الحالة',
        'priority'  => 'الأولوية',
    ],

    'priority' => [
        'high'   => 'مميزة',
        'normal' => 'عادية',
    ],

    'empty' => [
        'heading'     => 'لا توجد مراحل بعد',
        'description' => 'أنشئ مرحلة لبدء تنظيم المهام على اللوحة.',
    ],

    'progress' => [
        'label'   => 'توزيع حالات الأنشطة',
        'overdue' => 'متأخرة',
        'today'   => 'اليوم',
        'planned' => 'مخططة',
        'none'    => 'لا يوجد نشاط',
    ],

    'schedule' => [
        'heading'  => 'جدولة المهمة',
        'deadline' => 'الموعد النهائي',
    ],

    'activities' => [
        'heading'     => 'الأنشطة المجدولة',
        'schedule'    => 'جدولة نشاط',
        'deadline'    => 'تاريخ الاستحقاق',
        'assigned-to' => 'مُسند إلى',
    ],

    'assign' => [
        'heading'      => 'تعيين المهمة',
        'label'        => 'المعيَّنون',
        'save'         => 'تعيين',
        'view-profile' => 'عرض الملف الشخصي',
    ],

    'graph' => [
        'types' => [
            'bar'  => 'مخطط شريطي',
            'line' => 'مخطط خطي',
            'pie'  => 'مخطط دائري',
        ],
        'stacked'   => 'تكديس حسب الحالة',
        'sort-asc'  => 'ترتيب تصاعدي',
        'sort-desc' => 'ترتيب تنازلي',
    ],

    'notifications' => [
        'moved'             => 'تم نقل المهمة',
        'state-updated'     => 'تم تحديث الحالة',
        'assignees-updated' => 'تم تحديث المعيَّنين',
        'task-created'      => 'تم إنشاء المهمة',
        'task-deleted'      => 'تم حذف المهمة',
        'task-scheduled'    => 'تمت جدولة المهمة',
        'project-created'   => 'تم إنشاء المشروع',
        'project-updated'   => 'تم تحديث المشروع',
        'project-deleted'   => 'تم حذف المشروع',
        'project-in-use'    => 'لا يزال هذا المشروع يحتوي على مهام ولا يمكن حذفه',
        'stage-created'     => 'تم إنشاء المرحلة',
        'stage-updated'     => 'تم تحديث المرحلة',
        'stage-in-use'      => 'لا تزال هذه المرحلة تحتوي على مهام ولا يمكن حذفها',
        'stage-deleted'     => 'تم حذف المرحلة',
    ],
];
