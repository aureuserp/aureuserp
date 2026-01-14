<?php

namespace Webkul\FullCalendar;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FullCalendarPlugin implements Plugin
{
    protected array $plugins = ['dayGrid', 'timeGrid', 'interaction', 'list', 'moment', 'momentTimezone'];

    protected array $config = [];

    protected ?string $timezone = null;

    protected ?string $locale = null;

    protected ?bool $editable = null;

    protected ?bool $selectable = null;

    public function getId(): string
    {
        return 'full-calendar';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function setPlugins(array $plugins, bool $merge = true): static
    {
        $this->plugins = $merge ? array_merge($this->plugins, $plugins) : $plugins;

        return $this;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function setConfig(array $config): static
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setTimezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone ?? config('app.timezone');
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale ?? strtolower(str_replace('_', '-', app()->getLocale()));
    }

    public function editable(bool $editable = true): static
    {
        $this->editable = $editable;

        return $this;
    }

    public function isEditable(): bool
    {
        return $this->editable ?? data_get($this->config, 'editable', false);
    }

    public function selectable(bool $selectable = true): static
    {
        $this->selectable = $selectable;

        return $this;
    }

    public function isSelectable(): bool
    {
        return $this->selectable ?? data_get($this->config, 'selectable', false);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__.'/Filament/Resources',
                for: 'Webkul\\FullCalendar\\Filament\\Resources'
            )
            ->discoverPages(
                in: __DIR__.'/Filament/Pages',
                for: 'Webkul\\FullCalendar\\Filament\\Pages'
            )
            ->discoverClusters(
                in: __DIR__.'/Filament/Clusters',
                for: 'Webkul\\FullCalendar\\Filament\\Clusters'
            )
            ->discoverWidgets(
                in: __DIR__.'/Filament/Widgets',
                for: 'Webkul\\FullCalendar\\Filament\\Widgets'
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
