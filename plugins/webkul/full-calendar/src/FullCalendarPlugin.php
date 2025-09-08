<?php

namespace Webkul\FullCalendar;

use Filament\Contracts\Plugin;
use Filament\Panel;
use ReflectionClass;
use Webkul\Support\Package;

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
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel
            ->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Webkul\\FullCalendar\\Filament\\Resources')
            ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Webkul\\FullCalendar\\Filament\\Pages')
            ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Webkul\\FullCalendar\\Filament\\Clusters')
            ->discoverWidgets(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Webkul\\FullCalendar\\Filament\\Widgets');
    }

    public function boot(Panel $panel): void
    {
        //
    }

    protected function getPluginBasePath($path = null): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName()).($path ?? '');
    }
}
