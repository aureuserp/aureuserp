<?php

return [
    'navigation' => [
        'title' => 'الحقول المخصصة',
        'group' => 'الإعدادات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'name'              => 'الاسم',
                    'code'              => 'الرمز',
                    'code-helper-text'  => 'يجب أن يبدأ الرمز بحرف أو شرطة سفلية، ويمكن أن يحتوي فقط على أحرف وأرقام وشرطات سفلية.',
                ],
            ],

            'options' => [
                'title' => 'الخيارات',

                'fields' => [
                    'add-option' => 'إضافة خيار',
                ],
            ],

            'form-settings' => [
                'title' => 'إعدادات النموذج',

                'field-sets' => [
                    'validations' => [
                        'title' => 'التحققات',

                        'fields' => [
                            'validation'     => 'التحقق',
                            'field'          => 'الحقل',
                            'value'          => 'القيمة',
                            'add-validation' => 'إضافة تحقق',
                        ],
                    ],

                    'additional-settings' => [
                        'title' => 'إعدادات إضافية',

                        'fields' => [
                            'setting'     => 'الإعداد',
                            'value'       => 'القيمة',
                            'color'       => 'اللون',
                            'add-setting' => 'إضافة إعداد',

                            'color-options' => [
                                'danger'    => 'خطر',
                                'info'      => 'معلومات',
                                'primary'   => 'أساسي',
                                'secondary' => 'ثانوي',
                                'warning'   => 'تحذير',
                                'success'   => 'نجاح',
                            ],

                            'grid-options' => [
                                'row'    => 'صف',
                                'column' => 'عمود',
                            ],

                            'input-modes' => [
                                'text'     => 'نص',
                                'email'    => 'بريد إلكتروني',
                                'numeric'  => 'رقمي',
                                'integer'  => 'عدد صحيح',
                                'password' => 'كلمة مرور',
                                'tel'      => 'هاتف',
                                'url'      => 'رابط',
                                'color'    => 'لون',
                                'none'     => 'لا شيء',
                                'decimal'  => 'عشري',
                                'search'   => 'بحث',
                                'url'      => 'رابط',
                            ],
                        ],
                    ],
                ],

                'validations' => [
                    'common' => [
                        'gt'                   => 'أكبر من',
                        'gte'                  => 'أكبر من أو يساوي',
                        'lt'                   => 'أقل من',
                        'lte'                  => 'أقل من أو يساوي',
                        'max-size'             => 'الحجم الأقصى',
                        'min-size'             => 'الحجم الأدنى',
                        'multiple-of'          => 'مضاعف لـ',
                        'nullable'             => 'قابل للإلغاء',
                        'prohibited'           => 'محظور',
                        'prohibited-if'        => 'محظور إذا',
                        'prohibited-unless'    => 'محظور ما لم',
                        'prohibits'            => 'يحظر',
                        'required'             => 'مطلوب',
                        'required-if'          => 'مطلوب إذا',
                        'required-if-accepted' => 'مطلوب إذا تم القبول',
                        'required-unless'      => 'مطلوب ما لم',
                        'required-with'        => 'مطلوب مع',
                        'required-with-all'    => 'مطلوب مع الكل',
                        'required-without'     => 'مطلوب بدون',
                        'required-without-all' => 'مطلوب بدون الكل',
                        'rules'                => 'قواعد مخصصة',
                        'unique'               => 'فريد',
                    ],

                    'text' => [
                        'alpha-dash'        => 'أحرف وشرطات',
                        'alpha-num'         => 'أحرف وأرقام',
                        'ascii'             => 'ASCII',
                        'doesnt-end-with'   => 'لا ينتهي بـ',
                        'doesnt-start-with' => 'لا يبدأ بـ',
                        'ends-with'         => 'ينتهي بـ',
                        'filled'            => 'مملوء',
                        'ip'                => 'IP',
                        'ipv4'              => 'IPv4',
                        'ipv6'              => 'IPv6',
                        'length'            => 'الطول',
                        'mac-address'       => 'عنوان MAC',
                        'max-length'        => 'الحد الأقصى للطول',
                        'min-length'        => 'الحد الأدنى للطول',
                        'regex'             => 'تعبير منتظم',
                        'starts-with'       => 'يبدأ بـ',
                        'ulid'              => 'ULID',
                        'uuid'              => 'UUID',
                    ],

                    'textarea' => [
                        'filled'     => 'مملوء',
                        'max-length' => 'الحد الأقصى للطول',
                        'min-length' => 'الحد الأدنى للطول',
                    ],

                    'select' => [
                        'different'  => 'مختلف',
                        'exists'     => 'موجود',
                        'in'         => 'في',
                        'not-in'     => 'ليس في',
                        'same'       => 'مماثل',
                    ],

                    'radio' => [],

                    'checkbox' => [
                        'accepted' => 'مقبول',
                        'declined' => 'مرفوض',
                    ],

                    'toggle' => [
                        'accepted' => 'مقبول',
                        'declined' => 'مرفوض',
                    ],

                    'checkbox-list' => [
                        'in'        => 'في',
                        'max-items' => 'الحد الأقصى للعناصر',
                        'min-items' => 'الحد الأدنى للعناصر',
                    ],

                    'datetime' => [
                        'after'           => 'بعد',
                        'after-or-equal'  => 'بعد أو يساوي',
                        'before'          => 'قبل',
                        'before-or-equal' => 'قبل أو يساوي',
                    ],

                    'editor' => [
                        'filled'     => 'مملوء',
                        'max-length' => 'الحد الأقصى للطول',
                        'min-length' => 'الحد الأدنى للطول',
                    ],

                    'markdown' => [
                        'filled'     => 'مملوء',
                        'max-length' => 'الحد الأقصى للطول',
                        'min-length' => 'الحد الأدنى للطول',
                    ],

                    'color' => [
                        'hex-color' => 'لون سداسي عشري',
                    ],
                ],

                'settings' => [
                    'text' => [
                        'autocapitalize'    => 'تكبير تلقائي',
                        'autocomplete'      => 'إكمال تلقائي',
                        'autofocus'         => 'تركيز تلقائي',
                        'default'           => 'القيمة الافتراضية',
                        'disabled'          => 'معطل',
                        'helper-text'       => 'نص مساعد',
                        'hint'              => 'تلميح',
                        'hint-color'        => 'لون التلميح',
                        'hint-icon'         => 'أيقونة التلميح',
                        'id'                => 'المعرف',
                        'input-mode'        => 'وضع الإدخال',
                        'mask'              => 'قناع',
                        'placeholder'       => 'نص توضيحي',
                        'prefix'            => 'بادئة',
                        'prefix-icon'       => 'أيقونة البادئة',
                        'prefix-icon-color' => 'لون أيقونة البادئة',
                        'read-only'         => 'للقراءة فقط',
                        'step'              => 'خطوة',
                        'suffix'            => 'لاحقة',
                        'suffix-icon'       => 'أيقونة اللاحقة',
                        'suffix-icon-color' => 'لون أيقونة اللاحقة',
                    ],

                    'textarea' => [
                        'autofocus'    => 'تركيز تلقائي',
                        'autosize'     => 'حجم تلقائي',
                        'cols'         => 'أعمدة',
                        'default'      => 'القيمة الافتراضية',
                        'disabled'     => 'معطل',
                        'helperText'   => 'نص مساعد',
                        'hint'         => 'تلميح',
                        'hintColor'    => 'لون التلميح',
                        'hintIcon'     => 'أيقونة التلميح',
                        'id'           => 'المعرف',
                        'placeholder'  => 'نص توضيحي',
                        'read-only'    => 'للقراءة فقط',
                        'rows'         => 'صفوف',
                    ],

                    'select' => [
                        'default'                   => 'القيمة الافتراضية',
                        'disabled'                  => 'معطل',
                        'helper-text'               => 'نص مساعد',
                        'hint'                      => 'تلميح',
                        'hint-color'                => 'لون التلميح',
                        'hint-icon'                 => 'أيقونة التلميح',
                        'id'                        => 'المعرف',
                        'loading-message'           => 'رسالة التحميل',
                        'no-search-results-message' => 'رسالة عدم وجود نتائج',
                        'options-limit'             => 'حد الخيارات',
                        'preload'                   => 'تحميل مسبق',
                        'searchable'                => 'قابل للبحث',
                        'search-debounce'           => 'تأخير البحث',
                        'searching-message'         => 'رسالة البحث',
                        'search-prompt'             => 'موجه البحث',
                    ],

                    'radio' => [
                        'default'     => 'القيمة الافتراضية',
                        'disabled'    => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint'        => 'تلميح',
                        'hint-color'  => 'لون التلميح',
                        'hint-icon'   => 'أيقونة التلميح',
                        'id'          => 'المعرف',
                    ],

                    'checkbox' => [
                        'default'     => 'القيمة الافتراضية',
                        'disabled'    => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint'        => 'تلميح',
                        'hint-color'  => 'لون التلميح',
                        'hint-icon'   => 'أيقونة التلميح',
                        'id'          => 'المعرف',
                        'inline'      => 'مضمن',
                    ],

                    'toggle' => [
                        'default'     => 'القيمة الافتراضية',
                        'disabled'    => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint'        => 'تلميح',
                        'hint-color'  => 'لون التلميح',
                        'hint-icon'   => 'أيقونة التلميح',
                        'id'          => 'المعرف',
                        'off-color'   => 'لون الإيقاف',
                        'off-icon'    => 'أيقونة الإيقاف',
                        'on-color'    => 'لون التشغيل',
                        'on-icon'     => 'أيقونة التشغيل',
                    ],

                    'checkbox-list' => [
                        'bulk-toggleable'           => 'تبديل جماعي',
                        'columns'                   => 'أعمدة',
                        'default'                   => 'القيمة الافتراضية',
                        'disabled'                  => 'معطل',
                        'grid-direction'            => 'اتجاه الشبكة',
                        'helper-text'               => 'نص مساعد',
                        'hint'                      => 'تلميح',
                        'hint-color'                => 'لون التلميح',
                        'hint-icon'                 => 'أيقونة التلميح',
                        'id'                        => 'المعرف',
                        'max-items'                 => 'الحد الأقصى للعناصر',
                        'min-items'                 => 'الحد الأدنى للعناصر',
                        'no-search-results-message' => 'رسالة عدم وجود نتائج',
                        'searchable'                => 'قابل للبحث',
                    ],

                    'datetime' => [
                        'close-on-date-selection' => 'إغلاق عند اختيار التاريخ',
                        'default'                 => 'القيمة الافتراضية',
                        'disabled'                => 'معطل',
                        'disabled-dates'          => 'تواريخ معطلة',
                        'display-format'          => 'تنسيق العرض',
                        'first-fay-of-week'       => 'أول يوم في الأسبوع',
                        'format'                  => 'التنسيق',
                        'helper-text'             => 'نص مساعد',
                        'hint'                    => 'تلميح',
                        'hint-color'              => 'لون التلميح',
                        'hint-icon'               => 'أيقونة التلميح',
                        'hours-step'              => 'خطوة الساعات',
                        'id'                      => 'المعرف',
                        'locale'                  => 'اللغة',
                        'minutes-step'            => 'خطوة الدقائق',
                        'seconds'                 => 'ثواني',
                        'seconds-step'            => 'خطوة الثواني',
                        'timezone'                => 'المنطقة الزمنية',
                        'week-starts-on-monday'   => 'الأسبوع يبدأ الإثنين',
                        'week-starts-on-sunday'   => 'الأسبوع يبدأ الأحد',
                    ],

                    'editor' => [
                        'default'      => 'القيمة الافتراضية',
                        'disabled'     => 'معطل',
                        'helper-text'  => 'نص مساعد',
                        'hint'         => 'تلميح',
                        'hint-color'   => 'لون التلميح',
                        'hint-icon'    => 'أيقونة التلميح',
                        'id'           => 'المعرف',
                        'placeholder'  => 'نص توضيحي',
                        'read-only'    => 'للقراءة فقط',
                    ],

                    'markdown' => [
                        'default'      => 'القيمة الافتراضية',
                        'disabled'     => 'معطل',
                        'helper-text'  => 'نص مساعد',
                        'hint'         => 'تلميح',
                        'hint-color'   => 'لون التلميح',
                        'hint-icon'    => 'أيقونة التلميح',
                        'id'           => 'المعرف',
                        'placeholder'  => 'نص توضيحي',
                        'read-only'    => 'للقراءة فقط',
                    ],

                    'color' => [
                        'default'     => 'القيمة الافتراضية',
                        'disabled'    => 'معطل',
                        'helper-text' => 'نص مساعد',
                        'hint'        => 'تلميح',
                        'hint-color'  => 'لون التلميح',
                        'hint-icon'   => 'أيقونة التلميح',
                        'hsl'         => 'HSL',
                        'id'          => 'المعرف',
                        'rgb'         => 'RGB',
                        'rgba'        => 'RGBA',
                    ],

                    'file' => [
                        'accepted-file-types'                   => 'أنواع الملفات المقبولة',
                        'append-files'                          => 'إلحاق الملفات',
                        'deletable'                             => 'قابل للحذف',
                        'directory'                             => 'المجلد',
                        'downloadable'                          => 'قابل للتحميل',
                        'fetch-file-information'                => 'جلب معلومات الملف',
                        'file-attachments-directory'            => 'مجلد المرفقات',
                        'file-attachments-visibility'           => 'رؤية المرفقات',
                        'image'                                 => 'صورة',
                        'image-crop-aspect-ratio'               => 'نسبة قص الصورة',
                        'image-editor'                          => 'محرر الصور',
                        'image-editor-aspect-ratios'            => 'نسب محرر الصور',
                        'image-editor-empty-fill-color'         => 'لون تعبئة المحرر الفارغ',
                        'image-editor-mode'                     => 'وضع محرر الصور',
                        'image-preview-height'                  => 'ارتفاع معاينة الصورة',
                        'image-resize-mode'                     => 'وضع تغيير حجم الصورة',
                        'image-resize-target-height'            => 'ارتفاع الصورة المستهدف',
                        'image-resize-target-width'             => 'عرض الصورة المستهدف',
                        'loading-indicator-position'            => 'موضع مؤشر التحميل',
                        'move-files'                            => 'نقل الملفات',
                        'openable'                              => 'قابل للفتح',
                        'orient-images-from-exif'               => 'توجيه الصور من EXIF',
                        'panel-aspect-ratio'                    => 'نسبة اللوحة',
                        'panel-layout'                          => 'تخطيط اللوحة',
                        'previewable'                           => 'قابل للمعاينة',
                        'remove-uploaded-file-button-position'  => 'موضع زر حذف الملف',
                        'reorderable'                           => 'قابل لإعادة الترتيب',
                        'store-files'                           => 'تخزين الملفات',
                        'upload-button-position'                => 'موضع زر الرفع',
                        'uploading-message'                     => 'رسالة الرفع',
                        'upload-progress-indicator-position'    => 'موضع مؤشر تقدم الرفع',
                        'visibility'                            => 'الرؤية',
                    ],
                ],
            ],

            'table-settings' => [
                'title' => 'إعدادات الجدول',

                'fields' => [
                    'use-in-table'  => 'استخدام في الجدول',
                    'setting'       => 'الإعداد',
                    'value'         => 'القيمة',
                    'color'         => 'اللون',
                    'alignment'     => 'المحاذاة',
                    'font-weight'   => 'سمك الخط',
                    'icon-position' => 'موضع الأيقونة',
                    'size'          => 'الحجم',
                    'add-setting'   => 'إضافة إعداد',

                    'color-options' => [
                        'danger'    => 'خطر',
                        'info'      => 'معلومات',
                        'primary'   => 'أساسي',
                        'secondary' => 'ثانوي',
                        'warning'   => 'تحذير',
                        'success'   => 'نجاح',
                    ],

                    'alignment-options' => [
                        'start'   => 'البداية',
                        'left'    => 'يسار',
                        'center'  => 'وسط',
                        'end'     => 'النهاية',
                        'right'   => 'يمين',
                        'justify' => 'ضبط',
                        'between' => 'بين',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'خفيف جداً',
                        'light'       => 'خفيف',
                        'normal'      => 'عادي',
                        'medium'      => 'متوسط',
                        'semi-bold'   => 'شبه عريض',
                        'bold'        => 'عريض',
                        'extra-bold'  => 'عريض جداً',
                    ],

                    'icon-position-options' => [
                        'before'  => 'قبل',
                        'after'   => 'بعد',
                    ],

                    'size-options' => [
                        'extra-small' => 'صغير جداً',
                        'small'       => 'صغير',
                        'medium'      => 'متوسط',
                        'large'       => 'كبير',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'محاذاة للنهاية',
                        'alignment'              => 'المحاذاة',
                        'align-start'            => 'محاذاة للبداية',
                        'badge'                  => 'شارة',
                        'boolean'                => 'منطقي',
                        'color'                  => 'اللون',
                        'copyable'               => 'قابل للنسخ',
                        'copy-message'           => 'رسالة النسخ',
                        'copy-message-duration'  => 'مدة رسالة النسخ',
                        'default'                => 'افتراضي',
                        'filterable'             => 'قابل للتصفية',
                        'groupable'              => 'قابل للتجميع',
                        'grow'                   => 'نمو',
                        'icon'                   => 'أيقونة',
                        'icon-color'             => 'لون الأيقونة',
                        'icon-position'          => 'موضع الأيقونة',
                        'label'                  => 'التسمية',
                        'limit'                  => 'الحد',
                        'line-clamp'             => 'تقييد الأسطر',
                        'money'                  => 'مال',
                        'placeholder'            => 'نص توضيحي',
                        'prefix'                 => 'بادئة',
                        'searchable'             => 'قابل للبحث',
                        'size'                   => 'الحجم',
                        'sortable'               => 'قابل للفرز',
                        'suffix'                 => 'لاحقة',
                        'toggleable'             => 'قابل للتبديل',
                        'tooltip'                => 'تلميح',
                        'vertical-alignment'     => 'المحاذاة العمودية',
                        'vertically-align-start' => 'محاذاة عمودية للبداية',
                        'weight'                 => 'الوزن',
                        'width'                  => 'العرض',
                        'words'                  => 'كلمات',
                        'wrap-header'            => 'التفاف العنوان',
                        'column-span'            => 'امتداد العمود',
                        'helper-text'            => 'نص مساعد',
                        'hint'                   => 'تلميح',
                        'hint-color'             => 'لون التلميح',
                        'hint-icon'              => 'أيقونة التلميح',
                    ],

                    'datetime' => [
                        'date'              => 'التاريخ',
                        'date-time'         => 'التاريخ والوقت',
                        'date-time-tooltip' => 'تلميح التاريخ والوقت',
                        'since'             => 'منذ',
                    ],
                ],
            ],

            'infolist-settings' => [
                'title' => 'إعدادات قائمة المعلومات',

                'fields' => [
                    'setting'       => 'الإعداد',
                    'value'         => 'القيمة',
                    'color'         => 'اللون',
                    'font-weight'   => 'سمك الخط',
                    'icon-position' => 'موضع الأيقونة',
                    'size'          => 'الحجم',
                    'add-setting'   => 'إضافة إعداد',

                    'color-options' => [
                        'danger'    => 'خطر',
                        'info'      => 'معلومات',
                        'primary'   => 'أساسي',
                        'secondary' => 'ثانوي',
                        'warning'   => 'تحذير',
                        'success'   => 'نجاح',
                    ],

                    'font-weight-options' => [
                        'extra-light' => 'خفيف جداً',
                        'light'       => 'خفيف',
                        'normal'      => 'عادي',
                        'medium'      => 'متوسط',
                        'semi-bold'   => 'شبه عريض',
                        'bold'        => 'عريض',
                        'extra-bold'  => 'عريض جداً',
                    ],

                    'icon-position-options' => [
                        'before'  => 'قبل',
                        'after'   => 'بعد',
                    ],

                    'size-options' => [
                        'extra-small' => 'صغير جداً',
                        'small'       => 'صغير',
                        'medium'      => 'متوسط',
                        'large'       => 'كبير',
                    ],
                ],

                'settings' => [
                    'common' => [
                        'align-end'              => 'محاذاة للنهاية',
                        'alignment'              => 'المحاذاة',
                        'align-start'            => 'محاذاة للبداية',
                        'badge'                  => 'شارة',
                        'boolean'                => 'منطقي',
                        'color'                  => 'اللون',
                        'copyable'               => 'قابل للنسخ',
                        'copy-message'           => 'رسالة النسخ',
                        'copy-message-duration'  => 'مدة رسالة النسخ',
                        'default'                => 'افتراضي',
                        'filterable'             => 'قابل للتصفية',
                        'groupable'              => 'قابل للتجميع',
                        'grow'                   => 'نمو',
                        'icon'                   => 'أيقونة',
                        'icon-color'             => 'لون الأيقونة',
                        'icon-position'          => 'موضع الأيقونة',
                        'label'                  => 'التسمية',
                        'limit'                  => 'الحد',
                        'line-clamp'             => 'تقييد الأسطر',
                        'money'                  => 'مال',
                        'placeholder'            => 'نص توضيحي',
                        'prefix'                 => 'بادئة',
                        'searchable'             => 'قابل للبحث',
                        'size'                   => 'الحجم',
                        'sortable'               => 'قابل للفرز',
                        'suffix'                 => 'لاحقة',
                        'toggleable'             => 'قابل للتبديل',
                        'tooltip'                => 'تلميح',
                        'vertical-alignment'     => 'المحاذاة العمودية',
                        'vertically-align-start' => 'محاذاة عمودية للبداية',
                        'weight'                 => 'الوزن',
                        'width'                  => 'العرض',
                        'words'                  => 'كلمات',
                        'wrap-header'            => 'التفاف العنوان',
                        'column-span'            => 'امتداد العمود',
                        'helper-text'            => 'نص مساعد',
                        'hint'                   => 'تلميح',
                        'hint-color'             => 'لون التلميح',
                        'hint-icon'              => 'أيقونة التلميح',
                    ],

                    'datetime' => [
                        'date'              => 'التاريخ',
                        'date-time'         => 'التاريخ والوقت',
                        'date-time-tooltip' => 'تلميح التاريخ والوقت',
                        'since'             => 'منذ',
                    ],

                    'checkbox-list' => [
                        'separator'                => 'فاصل',
                        'list-with-line-breaks'    => 'قائمة بفواصل أسطر',
                        'bulleted'                 => 'نقطي',
                        'limit-list'               => 'تحديد القائمة',
                        'expandable-limited-list'  => 'قائمة محدودة قابلة للتوسيع',
                    ],

                    'select' => [
                        'separator'                => 'فاصل',
                        'list-with-line-breaks'    => 'قائمة بفواصل أسطر',
                        'bulleted'                 => 'نقطي',
                        'limit-list'               => 'تحديد القائمة',
                        'expandable-limited-list'  => 'قائمة محدودة قابلة للتوسيع',
                    ],

                    'checkbox' => [
                        'boolean'     => 'منطقي',
                        'false-icon'  => 'أيقونة خطأ',
                        'true-icon'   => 'أيقونة صحيح',
                        'true-color'  => 'لون صحيح',
                        'false-color' => 'لون خطأ',
                    ],

                    'toggle' => [
                        'boolean'     => 'منطقي',
                        'false-icon'  => 'أيقونة خطأ',
                        'true-icon'   => 'أيقونة صحيح',
                        'true-color'  => 'لون صحيح',
                        'false-color' => 'لون خطأ',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'fields' => [
                    'type'           => 'النوع',
                    'input-type'     => 'نوع الإدخال',
                    'is-multiselect' => 'اختيار متعدد',
                    'sort-order'     => 'ترتيب الفرز',

                    'type-options' => [
                        'text'          => 'حقل نص',
                        'textarea'      => 'منطقة نص',
                        'select'        => 'قائمة منسدلة',
                        'checkbox'      => 'مربع اختيار',
                        'radio'         => 'زر راديو',
                        'toggle'        => 'مفتاح تبديل',
                        'checkbox-list' => 'قائمة مربعات اختيار',
                        'datetime'      => 'منتقي التاريخ والوقت',
                        'editor'        => 'محرر نص غني',
                        'markdown'      => 'محرر Markdown',
                        'color'         => 'منتقي الألوان',
                    ],

                    'input-type-options' => [
                        'text'     => 'نص',
                        'email'    => 'بريد إلكتروني',
                        'numeric'  => 'رقمي',
                        'integer'  => 'عدد صحيح',
                        'password' => 'كلمة مرور',
                        'tel'      => 'هاتف',
                        'url'      => 'رابط',
                        'color'    => 'لون',
                    ],
                ],
            ],

            'resource' => [
                'title' => 'المورد',

                'fields' => [
                    'resource' => 'المورد',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'الرمز',
            'name'       => 'الاسم',
            'type'       => 'النوع',
            'resource'   => 'المورد',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'groups' => [
        ],

        'filters' => [
            'type'     => 'النوع',
            'resource' => 'المورد',

            'type-options' => [
                'text'          => 'حقل نص',
                'textarea'      => 'منطقة نص',
                'select'        => 'قائمة منسدلة',
                'checkbox'      => 'مربع اختيار',
                'radio'         => 'زر راديو',
                'toggle'        => 'مفتاح تبديل',
                'checkbox-list' => 'قائمة مربعات اختيار',
                'datetime'      => 'منتقي التاريخ والوقت',
                'editor'        => 'محرر نص غني',
                'markdown'      => 'محرر Markdown',
                'color'         => 'منتقي الألوان',
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الحقل',
                    'body'  => 'تم استعادة الحقل بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الحقل',
                    'body'  => 'تم حذف الحقل بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الحقل نهائياً',
                    'body'  => 'تم حذف الحقل نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الحقول',
                    'body'  => 'تم استعادة الحقول بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الحقول',
                    'body'  => 'تم حذف الحقول بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الحقول نهائياً',
                    'body'  => 'تم حذف الحقول نهائياً بنجاح.',
                ],
            ],
        ],
    ],
];
