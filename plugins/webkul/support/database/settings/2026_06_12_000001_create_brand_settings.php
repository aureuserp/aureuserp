<?php

use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('branding.primary_color', $this->defaultHex(Color::Blue));
        $this->migrator->add('branding.gray_color', $this->defaultHex(Color::Zinc));
        $this->migrator->add('branding.danger_color', $this->defaultHex(Color::Red));
        $this->migrator->add('branding.info_color', $this->defaultHex(Color::Blue));
        $this->migrator->add('branding.success_color', $this->defaultHex(Color::Green));
        $this->migrator->add('branding.warning_color', $this->defaultHex(Color::Amber));
        $this->migrator->add('branding.light_logo', 'images/logo.svg');
        $this->migrator->add('branding.dark_logo', 'images/logo.svg');
        $this->migrator->add('branding.favicon', 'images/favicon.ico');
        $this->migrator->add('branding.logo_height', '2rem');
    }

    private function defaultHex(array $color): string
    {
        return Color::convertToHex($color[600]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'primary_color')) {
            $this->migrator->delete('branding.primary_color');
            $this->migrator->delete('branding.gray_color');
            $this->migrator->delete('branding.danger_color');
            $this->migrator->delete('branding.info_color');
            $this->migrator->delete('branding.success_color');
            $this->migrator->delete('branding.warning_color');
            $this->migrator->delete('branding.light_logo');
            $this->migrator->delete('branding.dark_logo');
            $this->migrator->delete('branding.favicon');
            $this->migrator->delete('branding.logo_height');
        }
    }
};
