<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageCapacityByProducts extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = StorageCategoryResource::class;

    protected static string $relationship = 'storageCategoryCapacitiesByProduct';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.form.product'))
                    ->relationship(
                        'product',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($label) {
                        return str_contains($label, ' (Deleted)');
                    })
                    ->required()
                    ->unique(modifyRuleUsing: function (Unique $rule) {
                        return $rule->where('storage_category_id', $this->getOwnerRecord()->id);
                    })
                    ->searchable()
                    ->preload(),
                TextInput::make('qty')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.form.qty'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.table.columns.product')),
                TextColumn::make('qty')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.table.columns.qty')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.table.header-actions.create.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-products.table.actions.delete.notification.body')),
                    ),
            ])
            ->paginated(false);
    }
}
