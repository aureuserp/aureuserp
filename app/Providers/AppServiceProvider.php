<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * RTL languages list.
     */
    protected array $rtlLocales = ['ar', 'he', 'fa', 'ur'];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Language Switch for Admin Panel
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'ar'])
                ->labels([
                    'en' => 'English',
                    'ar' => 'العربية',
                ])
                ->flags([
                    'en' => asset('flags/en.svg'),
                    'ar' => asset('flags/ar.svg'),
                ])
                ->circular();
        });

        // Share RTL helper with all views
        view()->composer('*', function ($view) {
            $locale = app()->getLocale();
            $isRtl = in_array($locale, $this->rtlLocales);
            $direction = $isRtl ? 'rtl' : 'ltr';
            
            $view->with([
                'isRtl' => $isRtl,
                'direction' => $direction,
                'currentLocale' => $locale,
            ]);
        });

        // Register Blade directive for RTL check
        Blade::if('rtl', function () {
            return in_array(app()->getLocale(), $this->rtlLocales);
        });

        // Register Blade directive for direction
        Blade::directive('direction', function () {
            return "<?php echo in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr'; ?>";
        });

        // Add RTL script to Filament panels
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            fn () => new HtmlString($this->getRtlScript()),
        );

        // Add RTL CSS to Filament panels
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn () => new HtmlString($this->getRtlStyles()),
        );
    }

    /**
     * Get RTL script for setting document direction.
     */
    protected function getRtlScript(): string
    {
        $direction = in_array(app()->getLocale(), $this->rtlLocales) ? 'rtl' : 'ltr';
        $locale = app()->getLocale();
        
        return <<<HTML
        <script>
            document.documentElement.dir = '{$direction}';
            document.documentElement.lang = '{$locale}';
        </script>
        HTML;
    }

    /**
     * Get RTL styles for Filament panels.
     */
    protected function getRtlStyles(): string
    {
        if (!in_array(app()->getLocale(), $this->rtlLocales)) {
            return '';
        }

        return <<<HTML
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            /* Arabic Font */
            body, .fi-body {
                font-family: 'Cairo', 'Noto Sans Arabic', sans-serif !important;
            }
            
            /* RTL Base */
            [dir="rtl"] .fi-topbar { direction: rtl; }
            [dir="rtl"] .fi-sidebar { direction: rtl; }
            [dir="rtl"] .fi-main { direction: rtl; }
            [dir="rtl"] .fi-header { direction: rtl; }
            [dir="rtl"] .fi-simple-main { direction: rtl; }
            
            /* Form Fields */
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
            
            /* Buttons */
            [dir="rtl"] .fi-btn { flex-direction: row-reverse; }
            [dir="rtl"] .fi-btn > span + svg,
            [dir="rtl"] .fi-btn > svg + span { margin-left: 0; margin-right: 0.5rem; }
            
            /* Auth Pages */
            [dir="rtl"] .fi-simple-page { direction: rtl; text-align: right; }
            [dir="rtl"] .fi-simple-header { text-align: center; }
            [dir="rtl"] .fi-simple-main form { direction: rtl; }
            
            /* Links and Actions */
            [dir="rtl"] .fi-link { direction: rtl; }
            [dir="rtl"] .fi-ac { direction: rtl; }
            
            /* Dropdown */
            [dir="rtl"] .fi-dropdown-list { text-align: right; }
            
            /* Navigation */
            [dir="rtl"] .fi-topbar-nav { direction: rtl; }
            [dir="rtl"] nav { direction: rtl; }
            
            /* Cards and Sections */
            [dir="rtl"] .fi-section { direction: rtl; }
            [dir="rtl"] .fi-section-header { text-align: right; }
            
            /* Tables */
            [dir="rtl"] .fi-ta { direction: rtl; }
            [dir="rtl"] .fi-ta-header-cell { text-align: right; }
            [dir="rtl"] .fi-ta-cell { text-align: right; }
        </style>
        HTML;
    }

    /**
     * Check if current locale is RTL.
     */
    public static function isRtl(): bool
    {
        return in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']);
    }
}
