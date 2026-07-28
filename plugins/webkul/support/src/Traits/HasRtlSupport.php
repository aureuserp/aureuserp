<?php

namespace Webkul\Support\Traits;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

trait HasRtlSupport
{
    protected static function supportedLocales(): array
    {
        return config('app.supported_locales', []);
    }

    protected static function rtlLocales(): array
    {
        return array_keys(array_filter(
            static::supportedLocales(),
            fn ($meta) => ! empty($meta['rtl']),
        ));
    }

    protected function registerLanguageSwitch(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $locales = static::supportedLocales();

            $switch
                ->locales(array_keys($locales))
                ->labels(
                    collect($locales)
                        ->mapWithKeys(fn ($meta, $code) => [$code => $meta['native'] ?? $meta['label'] ?? $code])
                        ->all()
                )
                ->flags(
                    collect(array_keys($locales))
                        ->mapWithKeys(fn ($code) => [
                            $code => asset('flags/'.$code.'.svg'),
                        ])
                        ->all()
                )
                ->excludes(['admin', 'customer'])
                ->visible(insidePanels: false, outsidePanels: false)
                ->circular();
        });
    }

    protected function registerRtlSupport(): void
    {
        view()->composer('*', function ($view) {
            $locale = app()->getLocale();

            $isRtl = in_array($locale, static::rtlLocales(), true);

            $direction = $isRtl ? 'rtl' : 'ltr';

            $view->with([
                'isRtl'         => $isRtl,
                'direction'     => $direction,
                'currentLocale' => $locale,
            ]);
        });

        Blade::if('rtl', function () {
            return in_array(app()->getLocale(), static::rtlLocales(), true);
        });

        Blade::directive('direction', function () {
            return "<?php echo \\Webkul\\Support\\SupportServiceProvider::isRtl() ? 'rtl' : 'ltr'; ?>";
        });

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            fn () => view('support::rtl.script', ['rtlLocales' => static::rtlLocales()])->render(),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn () => view('support::rtl.styles', ['rtlLocales' => static::rtlLocales()])->render(),
        );
    }

    public static function isRtl(): bool
    {
        return in_array(app()->getLocale(), static::rtlLocales(), true);
    }
}
