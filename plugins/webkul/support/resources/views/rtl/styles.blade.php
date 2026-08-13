@if (in_array(app()->getLocale(), $rtlLocales))
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body, .fi-body {
            font-family: 'Cairo', 'Noto Sans Arabic', sans-serif !important;
        }

        [dir="rtl"] .fi-topbar { direction: rtl; }
        [dir="rtl"] .fi-sidebar { direction: rtl; }
        [dir="rtl"] .fi-main { direction: rtl; }
        [dir="rtl"] .fi-header { direction: rtl; }
        [dir="rtl"] .fi-simple-main { direction: rtl; }

        [dir="rtl"] .fi-fo-field-wrp { text-align: right; }
        [dir="rtl"] .fi-fo-field-wrp label { text-align: right; }
        [dir="rtl"] .fi-input-wrp { direction: rtl; }
        [dir="rtl"] input:not([type="email"]):not([type="url"]):not([type="tel"]),
        [dir="rtl"] textarea {
            text-align: right;
            direction: rtl;
        }
        [dir="rtl"] input[type="email"],
        [dir="rtl"] input[type="url"],
        [dir="rtl"] input[type="tel"] {
            direction: ltr;
            text-align: left;
        }

        [dir="rtl"] .fi-btn { flex-direction: row-reverse; }
        [dir="rtl"] .fi-btn > span + svg,
        [dir="rtl"] .fi-btn > svg + span { margin-left: 0; margin-right: 0.5rem; }

        [dir="rtl"] .fi-simple-page { direction: rtl; text-align: right; }
        [dir="rtl"] .fi-simple-header { text-align: center; }
        [dir="rtl"] .fi-simple-main form { direction: rtl; }

        [dir="rtl"] .fi-link { direction: rtl; }
        [dir="rtl"] .fi-ac { direction: rtl; }

        [dir="rtl"] .fi-dropdown-list { text-align: right; }

        [dir="rtl"] .fi-topbar-nav { direction: rtl; }
        [dir="rtl"] nav { direction: rtl; }

        [dir="rtl"] .fi-section { direction: rtl; }
        [dir="rtl"] .fi-section-header { text-align: right; }

        [dir="rtl"] .fi-ta { direction: rtl; }
        [dir="rtl"] .fi-ta-header-cell { text-align: right; }
        [dir="rtl"] .fi-ta-cell { text-align: right; }
    </style>
@endif
