<?php

namespace App\Support;

use Filament\Support\Colors\Color;

class BrandPalette
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

    /**
     * @return array<int, string>
     */
    public static function fromHex(string $seed): array
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
