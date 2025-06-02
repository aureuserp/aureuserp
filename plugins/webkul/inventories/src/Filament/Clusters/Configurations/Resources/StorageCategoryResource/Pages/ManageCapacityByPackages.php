<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource;
use Webkul\Inventory\Settings\OperationSettings;

class ManageCapacityByPackages extends ManageRelatedRecords
{
    protected static string $resource = StorageCategoryResource::class;

    protected static string $relationship = 'storageCategoryCapacitiesByPackageType';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function canAccess(array $parameters = []): bool
    {
        $canAccess = parent::canAccess($parameters);

        if (! $canAccess) {
            return false;
        }

        return app(OperationSettings::class)->enable_packages;
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('package_type_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.form.package-type'))
                    ->relationship(
                        'packageType',
                        'name',
                    )
                    ->required()
                    ->unique(modifyRuleUsing: function (Unique $rule) {
                        return $rule->where('storage_category_id', $this->getOwnerRecord()->id);
                    })
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('qty')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.form.qty'))
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
                Tables\Columns\TextColumn::make('packageType.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.table.columns.package-type')),
                Tables\Columns\TextColumn::make('qty')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.table.columns.qty')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.table.header-actions.create.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.table.header-actions.create.notification.body')),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/storage-category/pages/manage-capacity-by-packages.table.actions.delete.notification.body')),
                    ),
            ])
            ->paginated(false);
    }
}
