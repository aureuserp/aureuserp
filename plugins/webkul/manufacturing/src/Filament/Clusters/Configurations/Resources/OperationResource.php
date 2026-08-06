<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Manufacturing\Filament\Clusters\Configurations;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Pages\CreateOperation;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Pages\EditOperation;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Pages\ListOperations;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Pages\ViewOperation;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Schemas\OperationForm;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Schemas\OperationInfolist;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Tables\OperationsTable;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Settings\OperationSettings;

class OperationResource extends Resource
{
    protected static ?string $model = Operation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(OperationSettings::class)->enable_work_orders;
    }

    public static function getModelLabel(): string
    {
        return __('manufacturing::models/operation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('manufacturing::filament/clusters/configurations/resources/operation.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/configurations/resources/operation.navigation.title');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public static function form(Schema $schema): Schema
    {
        return OperationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OperationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationInfolist::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOperations::route('/'),
            'create' => CreateOperation::route('/create'),
            'view'   => ViewOperation::route('/{record}'),
            'edit'   => EditOperation::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewOperation::class,
            EditOperation::class,
        ]);
    }

    public static function getBillOfMaterialLabel(?BillOfMaterial $billOfMaterial): string
    {
        if (! $billOfMaterial) {
            return '—';
        }

        return $billOfMaterial->code
            ?: ($billOfMaterial->product?->name ?? (string) $billOfMaterial->getKey());
    }
}
