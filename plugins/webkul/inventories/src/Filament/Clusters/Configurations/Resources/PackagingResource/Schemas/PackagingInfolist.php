<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource;
use Webkul\Product\Filament\Resources\PackagingResource as BasePackagingResource;

class PackagingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $schema = BasePackagingResource::infolist($schema);

        $components = $schema->getComponents();

        $firstSectionChildComponents = $components[0]->getDefaultChildComponents();

        $firstSectionChildComponents[] = TextEntry::make('packageType.name')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.general.entries.package_type'))
            ->icon('heroicon-o-archive-box')
            ->placeholder('—')
            ->visible(PackagingResource::getOperationSettings()->enable_packages);

        $components[0]->childComponents($firstSectionChildComponents);

        array_splice($components, 1, 0, [
            Section::make(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.routing.title'))
                ->schema([
                    RepeatableEntry::make('routes')
                        ->label(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.routing.entries.routes'))
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('inventories::filament/clusters/configurations/resources/packaging.infolist.sections.routing.entries.route_name'))
                                ->icon('heroicon-o-truck'),
                        ])
                        ->placeholder('—')
                        ->columns(1),
                ])
                ->collapsible()
                ->visible(PackagingResource::getWarehouseSettings()->enable_multi_steps_routes),
        ]);

        $schema->components($components);

        return $schema;
    }
}
