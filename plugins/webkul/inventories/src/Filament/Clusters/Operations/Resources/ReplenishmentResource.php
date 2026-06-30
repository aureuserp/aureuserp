<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource\Pages\ManageReplenishment;
use Webkul\Inventory\Models\OrderPoint;

class ReplenishmentResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = OrderPoint::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-up-down';

    protected static ?int $navigationSort = 4;

    // TODO: Remove this when completed
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Operations::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/replenishment.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/replenishment.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema(static::getCustomFormFields())
                    ->columns(2)
                    ->visible(fn () => filled(static::getCustomFormFields())),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns(static::mergeCustomTableColumns([

            ]))
            ->groups(
                collect([
                ])->filter(function ($group) {
                    return match ($group->getId()) {
                        default        => true
                    };
                })->all()
            )
            ->filters(static::mergeCustomTableFilters([
                QueryBuilder::make()
                    ->constraints(collect([
                    ])->filter()->values()->all()),
            ]), layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->headerActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/operations/resources/replenishment.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {

                        return $data;
                    })
                    ->before(function (array $data) {})
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/replenishment.table.header-actions.create.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/replenishment.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ManageReplenishment::route('/'),
        ];
    }
}
