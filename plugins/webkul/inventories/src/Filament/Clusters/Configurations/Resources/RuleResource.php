<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\HtmlString;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\ManageRules;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\RelationManagers\RulesRelationManager;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages\CreateRule;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages\EditRule;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages\ListRules;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages\ViewRule;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Partner\Filament\Resources\PartnerResource;

class RuleResource extends Resource
{
    protected static ?string $model = Rule::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(WarehouseSettings::class)->enable_multi_steps_routes;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/rule.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/rule.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Select::make('action')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.action'))
                                                    ->required()
                                                    ->options(RuleAction::class)
                                                    ->default(RuleAction::PULL->value)
                                                    ->selectablePlaceholder(false)
                                                    ->live(),
                                                Select::make('operation_type_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.operation-type'))
                                                    ->relationship('operationType', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->getOptionLabelFromRecordUsing(function (OperationType $record) {
                                                        if (! $record->warehouse) {
                                                            return $record->name;
                                                        }

                                                        return $record->warehouse->name.': '.$record->name;
                                                    })
                                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                                        $operationType = OperationType::find($get('operation_type_id'));

                                                        $set('source_location_id', $operationType?->source_location_id);

                                                        $set('destination_location_id', $operationType?->destination_location_id);
                                                    })
                                                    ->live(),
                                                Select::make('source_location_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.source-location'))
                                                    ->relationship('sourceLocation', 'full_name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),
                                                Select::make('destination_location_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'))
                                                    ->relationship('destinationLocation', 'full_name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),
                                            ]),

                                        Group::make()
                                            ->schema([
                                                Placeholder::make('placeholder')
                                                    ->hiddenLabel()
                                                    ->content(new HtmlString('When products are needed in Destination Location, </br>Operation Type are created from Source Location to fulfill the need.'))
                                                    ->content(function (Get $get): HtmlString {
                                                        $operation = OperationType::find($get('operation_type_id'));

                                                        $pullMessage = __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.action-information.pull', [
                                                            'sourceLocation'      => $operation?->sourceLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'),
                                                            'operation'           => $operation?->name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.operation-type'),
                                                            'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.source-location'),
                                                        ]);

                                                        $pushMessage = __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.action-information.push', [
                                                            'sourceLocation'      => $operation?->sourceLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.source-location'),
                                                            'operation'           => $operation?->name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.operation-type'),
                                                            'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'),
                                                        ]);

                                                        $buyMessage = __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.action-information.buy', [
                                                            'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'),
                                                        ]);

                                                        $action = ($get('action') instanceof RuleAction)
                                                            ? $get('action')
                                                            : RuleAction::tryFrom($get('action') ?? RuleAction::PULL->value);

                                                        return match ($action) {
                                                            RuleAction::PULL      => new HtmlString($pullMessage),
                                                            RuleAction::PUSH      => new HtmlString($pushMessage),
                                                            RuleAction::PULL_PUSH => new HtmlString($pullMessage.'</br></br>'.$pushMessage),
                                                            default               => new HtmlString($buyMessage),
                                                        };
                                                    }),
                                            ]),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.title'))
                            ->schema([
                                Select::make('partner_address_id')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.partner-address'))
                                    ->relationship('partnerAddress', 'name')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: new HtmlString(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.partner-address-hint-tooltip')))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                                    ->hidden(fn (Get $get): bool => $get('action') == RuleAction::PUSH->value),
                                TextInput::make('delay')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.lead-time'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: new HtmlString(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.lead-time-hint-tooltip')))
                                    ->integer()
                                    ->minValue(0),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fieldsets.applicability.title'))
                                    ->schema([
                                        Select::make('route_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fieldsets.applicability.fields.route'))
                                            ->relationship(
                                                'route',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->hiddenOn([ManageRules::class, RulesRelationManager::class]),
                                        Select::make('company_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fieldsets.applicability.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columns(1),
                            ]),
                    ]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('action')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.action'))
                    ->searchable(),
                TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.source-location'))
                    ->searchable(),
                TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.destination-location'))
                    ->searchable(),
                TextColumn::make('route.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.route'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.name'))
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('action')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.action'))
                    ->collapsible(),
                Tables\Grouping\Group::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.source-location'))
                    ->collapsible(),
                Tables\Grouping\Group::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.destination-location'))
                    ->collapsible(),
                Tables\Grouping\Group::make('route.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.route'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.action'))
                    ->options(RuleAction::class),
                SelectFilter::make('source_location_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.source-location'))
                    ->relationship('sourceLocation', 'full_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('destination_location_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.destination-location'))
                    ->relationship('destinationLocation', 'full_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('route_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.route'))
                    ->relationship('route', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Rule $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.force-delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.force-delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.title'))
                            ->description(function (Rule $record) {
                                $operation = $record->operationType;

                                $pullMessage = __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.description.pull', [
                                    'sourceLocation'      => $operation?->sourceLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.destination-location'),
                                    'operation'           => $operation?->name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.operation-type'),
                                    'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.source-location'),
                                ]);

                                $pushMessage = __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.description.push', [
                                    'sourceLocation'      => $operation?->sourceLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.source-location'),
                                    'operation'           => $operation?->name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.operation-type'),
                                    'destinationLocation' => $operation?->destinationLocation?->full_name ?? __('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.destination-location'),
                                ]);

                                return match ($record->action) {
                                    RuleAction::PULL      => new HtmlString($pullMessage),
                                    RuleAction::PUSH      => new HtmlString($pushMessage),
                                    RuleAction::PULL_PUSH => new HtmlString($pullMessage.'</br></br>'.$pushMessage),
                                };
                            })
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.name'))
                                                    ->icon('heroicon-o-document-text')
                                                    ->size(TextSize::Large)
                                                    ->weight(FontWeight::Bold),

                                                TextEntry::make('action')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.action'))
                                                    ->icon('heroicon-o-arrows-right-left')
                                                    ->badge(),

                                                TextEntry::make('operationType.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.operation-type'))
                                                    ->icon('heroicon-o-briefcase'),

                                                TextEntry::make('sourceLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.source-location'))
                                                    ->icon('heroicon-o-map-pin'),

                                                TextEntry::make('destinationLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.destination-location'))
                                                    ->icon('heroicon-o-map-pin'),
                                            ]),

                                        Group::make()
                                            ->schema([
                                                TextEntry::make('route.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.route'))
                                                    ->icon('heroicon-o-globe-alt'),

                                                TextEntry::make('company.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.company'))
                                                    ->icon('heroicon-o-building-office'),

                                                TextEntry::make('partner_address_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.partner-address'))
                                                    ->icon('heroicon-o-user-group')
                                                    ->getStateUsing(fn ($record) => $record->partnerAddress?->name)
                                                    ->placeholder('—'),

                                                TextEntry::make('delay')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.lead-time'))
                                                    ->icon('heroicon-o-clock')
                                                    ->suffix(' days')
                                                    ->placeholder('0'),
                                            ]),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRules::route('/'),
            'create' => CreateRule::route('/create'),
            'view'   => ViewRule::route('/{record}'),
            'edit'   => EditRule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['sourceLocation' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['destinationLocation' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['route' => function ($query) {
                $query->withTrashed();
            }]);
    }
}
