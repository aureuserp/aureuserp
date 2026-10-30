<?php

return [
    'move' => [
        'no-journal-found-title' => 'لم يتم إعداد المحاسبة',
        'no-journal-found'       => 'لم يتم العثور على دفتر يومية في شركة :company لأي من هذه الأنواع: :types. يرجى إعداد شجرة الحسابات لهذه الشركة أولاً.',
    ],

    'posting' => [
        'invalid-entry'         => 'قيد اليومية غير صالح: :field.',
        'unbalanced'            => 'قيد اليومية :move غير متوازن: المدين :debit، الدائن :credit.',
        'rejected-by-validator' => 'تم رفض الترحيل: :reason',
        'move-not-postable'     => 'لا يمكن ترحيل قيد اليومية: :reason',
        'move-not-reversible'   => 'لا يمكن عكس قيد اليومية :move.',
        'duplicate-source'      => 'تم استخدام مفتاح المصدر بالفعل في قيد اليومية :move بسطور مختلفة.',
    ],

    'tax-formula' => [
        'empty'                => 'لا يمكن أن تكون الصيغة فارغة.',
        'invalid-character'    => 'تحتوي الصيغة على حرف غير صالح: :character',
        'unexpected-token'     => 'تحتوي الصيغة على عنصر غير متوقع: :token',
        'unexpected-end'       => 'انتهت الصيغة بشكل غير متوقع.',
        'unclosed-parenthesis' => 'تحتوي الصيغة على قوس غير مغلق.',
        'unknown-variable'     => 'متغير غير معروف ":variable". المتغيرات المتاحة فقط هي: :variables',
        'unknown-function'     => 'دالة غير معروفة ":function". الدوال المتاحة فقط هي: :functions',
    ],
];
