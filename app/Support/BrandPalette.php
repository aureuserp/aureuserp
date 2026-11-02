<?php

namespace App\Support;

use Filament\Support\Colors\Color;

class BrandPalette
{
    protected const ANCHOR_SHADE = 600;

    protected const LIGHTNESS = [
        50  => 0.971,
        100 => 0.936,
        200 => 0.885,
        300 => 0.808,
        400 => 0.704,
        500 => 0.625,
        600 => 0.576,
        700 => 0.505,
        800 => 0.444,
        900 => 0.396,
        950 => 0.258,
    ];

    protected const CHROMA = [
        50  => 0.011,
        100 => 0.027,
        200 => 0.051,
        300 => 0.095,
        400 => 0.159,
        500 => 0.199,
        600 => 0.203,
        700 => 0.177,
        800 => 0.147,
        900 => 0.117,
        950 => 0.076,
    ];

    protected const GRAY_LIGHTNESS = [
        50  => 0.985,
        100 => 0.970,
        200 => 0.922,
        300 => 0.870,
        400 => 0.708,
        500 => 0.556,
        600 => 0.439,
        700 => 0.371,
        800 => 0.269,
        900 => 0.205,
        950 => 0.145,
    ];

    protected const GRAY_CHROMA = [
        50  => 0.002,
        100 => 0.003,
        200 => 0.004,
        300 => 0.006,
        400 => 0.008,
        500 => 0.010,
        600 => 0.008,
        700 => 0.006,
        800 => 0.005,
        900 => 0.005,
        950 => 0.004,
    ];

    public static function hueOf(string $seed): float
    {
        $oklch = str_starts_with($seed, 'oklch(') ? $seed : Color::convertToOklch($seed);

        [, , $hue] = sscanf($oklch, 'oklch(%f %f %f)');

        return $hue ?? 0.0;
    }

    /**
     * @return array<int, string>
     */
    public static function fromHex(string $seed, ?float $tintHue = null): array
    {
        [$lightness, $chroma, $hue] = sscanf(Color::convertToOklch($seed), 'oklch(%f %f %f)');

        $isAchromatic = $chroma < 0.03;

        $isTintedGray = $isAchromatic && $tintHue !== null;

        $lightnessCurve = $isAchromatic ? self::GRAY_LIGHTNESS : self::LIGHTNESS;

        $anchorLightness = $lightnessCurve[self::ANCHOR_SHADE];
        $anchorChroma = self::CHROMA[self::ANCHOR_SHADE];

        $chromaScale = $anchorChroma > 0 ? $chroma / $anchorChroma : 0.0;

        $outputHue = $isTintedGray ? $tintHue : $hue;

        $palette = [];

        foreach ($lightnessCurve as $shade => $referenceLightness) {
            if ($referenceLightness >= $anchorLightness) {
                $newLightness = $lightness
                    + (($referenceLightness - $anchorLightness) / (1 - $anchorLightness)) * (1 - $lightness);
            } else {
                $newLightness = ($referenceLightness / $anchorLightness) * $lightness;
            }

            $newLightness = max(0.0, min(1.0, $newLightness));

            $newChroma = match (true) {
                $isTintedGray => self::GRAY_CHROMA[$shade],
                $isAchromatic => 0.0,
                default       => round(self::CHROMA[$shade] * $chromaScale, 5),
            };

            $palette[$shade] = 'oklch('.round($newLightness, 5).' '.$newChroma.' '.round($outputHue, 3).')';
        }

        return $palette;
    }
}
