<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\PartnerLevelResource\Pages\ManagePartnerLevels;
use Webkul\Crm\Models\PartnerLevel;

class PartnerLevelResource extends Resource
{
    protected static ?string $model = PartnerLevel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/partner-level.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/partner-level.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/partner-level.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/partner-level.navigation.group');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function form(Schema $schema): Schema
    {
        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/partner-level.form.'.$key);

        return $schema
            ->components([
                TextInput::make('name')
                    ->label($label('name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('partner_weight')
                    ->label($label('weight'))
                    ->helperText($label('weight-helper'))
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(1)
                    ->required(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/partner-level.'.$key);

        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount('partners'))
            ->columns([
                TextColumn::make('name')
                    ->label($label('table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('partner_weight')
                    ->label($label('table.columns.weight'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('partners_count')
                    ->label($label('table.columns.partners'))
                    ->numeric(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([
                EditAction::make()
                    ->hidden(fn (PartnerLevel $record): bool => $record->trashed())
                    ->successNotification(Notification::make()->success()->title($label('notifications.updated'))),
                RestoreAction::make()
                    ->successNotification(Notification::make()->success()->title($label('notifications.restored'))),
                DeleteAction::make()
                    ->label($label('actions.archive'))
                    ->icon('heroicon-o-archive-box')
                    ->modalHeading($label('actions.archive-heading'))
                    ->modalDescription($label('actions.archive-description'))
                    ->modalSubmitActionLabel($label('actions.archive'))
                    ->successNotification(Notification::make()->success()->title($label('notifications.archived'))),
                ForceDeleteAction::make()
                    ->successNotification(Notification::make()->success()->title($label('notifications.deleted'))),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make(),
                    DeleteBulkAction::make()
                        ->label($label('actions.archive'))
                        ->icon('heroicon-o-archive-box')
                        ->modalHeading($label('actions.archive-heading'))
                        ->modalDescription($label('actions.archive-description'))
                        ->modalSubmitActionLabel($label('actions.archive')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePartnerLevels::route('/'),
        ];
    }
}
