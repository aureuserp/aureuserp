<?php

namespace App\Http\Middleware;

use App\Support\BrandPalette;
use Closure;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Colors\ColorManager;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Webkul\Support\Settings\BrandSettings;

class ApplyBrandSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        $panel = Filament::getCurrentPanel();

        if ($panel === null) {
            return $next($request);
        }

        try {
            $brand = settings(BrandSettings::class);

            $panelDefaultColors = array_merge(
                ColorManager::DEFAULT_COLORS,
                $panel->getColors(),
            );

            $colorKeys = ['primary', 'success', 'danger', 'warning', 'info', 'gray'];

            $brandColors = [];

            foreach ($colorKeys as $colorKey) {
                $brandValue = $brand->{$colorKey.'_color'};

                if (empty($brandValue)) {
                    continue;
                }

                $default = $panelDefaultColors[$colorKey][600] ?? null;

                if ($default !== null && $this->isSameColor($brandValue, $default)) {
                    continue;
                }

                $brandColors[$colorKey] = $this->paletteFromHex($brandValue);
            }

            if ($brandColors !== []) {
                FilamentColor::register($brandColors);
            }

            if (! empty($brand->light_logo)) {
                $panel->brandLogo($this->assetUrl($brand->light_logo));
            }

            if (! empty($brand->dark_logo)) {
                $panel->darkModeBrandLogo($this->assetUrl($brand->dark_logo));
            }

            if (! empty($brand->favicon)) {
                $panel->favicon($this->assetUrl($brand->favicon));
            }

            if (! empty($brand->logo_height)) {
                $panel->brandLogoHeight($brand->logo_height);
            }
        } catch (Throwable) {
        }

        return $next($request);
    }

    protected function assetUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset($path);
    }

    protected function isSameColor(string $a, string $b): bool
    {
        return Color::convertToHex($a) === Color::convertToHex($b);
    }

    protected function paletteFromHex(string $seed): array
    {
        return BrandPalette::fromHex($seed);
    }
}
