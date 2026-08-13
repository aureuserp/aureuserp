<?php

namespace App\Http\Middleware;

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
    protected const ANCHOR_SHADE = 600;

    protected const LIGHTNESS = [
        50  => 0.97718,
        100 => 0.95035,
        200 => 0.90547,
        300 => 0.84047,
        400 => 0.75353,
        500 => 0.68271,
        600 => 0.59782,
        700 => 0.51494,
        800 => 0.44612,
        900 => 0.39459,
        950 => 0.27788,
    ];

    protected const CHROMA = [
        50  => 0.01395,
        100 => 0.03273,
        200 => 0.06318,
        300 => 0.10605,
        400 => 0.15027,
        500 => 0.17009,
        600 => 0.16914,
        700 => 0.14941,
        800 => 0.12332,
        900 => 0.09964,
        950 => 0.07136,
    ];

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
        [$lightness, $chroma, $hue] = sscanf(Color::convertToOklch($seed), 'oklch(%f %f %f)');

        $anchorLightness = self::LIGHTNESS[self::ANCHOR_SHADE];
        $anchorChroma = self::CHROMA[self::ANCHOR_SHADE];

        $isAchromatic = $chroma < 0.03;
        $chromaScale = $anchorChroma > 0 ? $chroma / $anchorChroma : 0.0;

        $palette = [];

        foreach (self::LIGHTNESS as $shade => $referenceLightness) {
            if ($referenceLightness >= $anchorLightness) {
                $newLightness = $lightness
                    + (($referenceLightness - $anchorLightness) / (1 - $anchorLightness)) * (1 - $lightness);
            } else {
                $newLightness = ($referenceLightness / $anchorLightness) * $lightness;
            }

            $newLightness = max(0.0, min(1.0, $newLightness));
            $newChroma = $isAchromatic ? 0.0 : round(self::CHROMA[$shade] * $chromaScale, 5);

            $palette[$shade] = 'oklch('.round($newLightness, 5).' '.$newChroma.' '.round($hue, 3).')';
        }

        return $palette;
    }
}
