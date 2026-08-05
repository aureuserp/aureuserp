<?php

namespace Webkul\Field\Filament\Resources;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Webkul\Field\Filament\Resources\FieldResource\Pages\CreateField;
use Webkul\Field\Filament\Resources\FieldResource\Pages\EditField;
use Webkul\Field\Filament\Resources\FieldResource\Pages\ListFields;
use Webkul\Field\Filament\Resources\FieldResource\Schemas\FieldForm;
use Webkul\Field\Filament\Resources\FieldResource\Tables\FieldsTable;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Field\Models\Field;
use Webkul\Support\Enums\NavigationGroup;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('fields::filament/resources/field.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('fields::filament/resources/field.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function form(Schema $schema): Schema
    {
        return FieldForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FieldsTable::configure($table);
    }

    protected static function adminPanelResources(): array
    {
        return Filament::getPanel('admin')->getResources();
    }

    public static function getCustomizableResourceOptions(?string $plugin = null): array
    {
        return collect(static::customizableResources())
            ->when(filled($plugin), fn ($resources) => $resources->filter(
                fn ($resource) => static::resourcePluginDirectory($resource) === $plugin
            ))
            ->mapWithKeys(fn ($resource) => [$resource::getModel() => static::resourceLabel($resource)])
            ->sort()
            ->toArray();
    }

    public static function customizableResources(): array
    {
        static $resources = null;

        return $resources ??= collect(static::adminPanelResources())
            ->filter(fn ($resource) => static::isCustomizableResource($resource))
            ->values()
            ->all();
    }

    protected static function isCustomizableResource(string $resource): bool
    {
        return is_subclass_of($resource, Resource::class)
            && in_array(HasCustomFields::class, class_uses_recursive($resource), true)
            && static::resourcePluginIsAvailable($resource)
            && static::resourceRendersCustomFields($resource);
    }

    protected static function resourcePluginIsAvailable(string $resource): bool
    {
        return ! in_array(static::resourcePluginDirectory($resource), static::disabledPluginDirectories(), true);
    }

    protected static function disabledPluginDirectories(): array
    {
        static $directories = null;

        return $directories ??= DB::table('plugins')
            ->where(fn ($query) => $query->where('is_installed', false)->orWhere('is_active', false))
            ->pluck('name')
            ->all();
    }

    public static function resourcePluginDirectory(string $resource): string
    {
        static $cache = [];

        if (! array_key_exists($resource, $cache)) {
            $file = (string) (new \ReflectionClass($resource))->getFileName();

            $cache[$resource] = preg_match('#/plugins/webkul/([^/]+)/#', $file, $matches) ? $matches[1] : '';
        }

        return $cache[$resource];
    }

    /**
     * Whether the form() this resource actually runs appends its own custom fields.
     *
     * A resource whose form() hands off to another resource's form() renders that
     * resource's model, so fields registered against this one never show up.
     */
    protected static function resourceRendersCustomFields(string $resource): bool
    {
        static $cache = [];

        if (array_key_exists($resource, $cache)) {
            return $cache[$resource];
        }

        if (! method_exists($resource, 'form')) {
            return $cache[$resource] = false;
        }

        $method = new \ReflectionMethod($resource, 'form');

        $file = $method->getFileName();

        if ($file === false || ! is_readable($file)) {
            return $cache[$resource] = false;
        }

        $body = implode('', array_slice(
            file($file),
            $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1,
        ));

        return $cache[$resource] = str_contains($body, 'getCustomFormFields')
            || str_contains($body, 'mergeCustomFormFields');
    }

    protected static function resourceLabel(string $resource): string
    {
        return str(class_basename($resource))->beforeLast('Resource')->headline()->toString();
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFields::route('/'),
            'create' => CreateField::route('/create'),
            'edit'   => EditField::route('/{record}/edit'),
        ];
    }
}
