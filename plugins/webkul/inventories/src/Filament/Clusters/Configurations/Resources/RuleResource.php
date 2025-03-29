<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\ManageRules;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\RelationManagers\RulesRelationManager;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Partner\Filament\Resources\AddressResource;

class RuleResource extends Resource
{
    protected static ?string $model = Rule::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.title'))
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Select::make('action')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.action'))
                                                    ->required()
                                                    ->options(Enums\RuleAction::class)
                                                    ->default(Enums\RuleAction::PULL->value)
                                                    ->selectablePlaceholder(false)
                                                    ->live(),
                                                Forms\Components\Select::make('operation_type_id')
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
                                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                                        $operationType = OperationType::find($get('operation_type_id'));

                                                        $set('source_location_id', $operationType?->source_location_id);

                                                        $set('destination_location_id', $operationType?->destination_location_id);
                                                    })
                                                    ->live(),
                                                Forms\Components\Select::make('source_location_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.source-location'))
                                                    ->relationship('sourceLocation', 'full_name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),
                                                Forms\Components\Select::make('destination_location_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.general.fields.destination-location'))
                                                    ->relationship('destinationLocation', 'full_name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),
                                            ]),

                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Placeholder::make('')
                                                    ->hiddenLabel()
                                                    ->content(new HtmlString('When products are needed in Destination Location, </br>Operation Type are created from Source Location to fulfill the need.'))
                                                    ->content(function (Forms\Get $get): HtmlString {
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

                                                        return match ($get('action') ?? Enums\RuleAction::PULL->value) {
                                                            Enums\RuleAction::PULL->value      => new HtmlString($pullMessage),
                                                            Enums\RuleAction::PUSH->value      => new HtmlString($pushMessage),
                                                            Enums\RuleAction::PULL_PUSH->value => new HtmlString($pullMessage.'</br></br>'.$pushMessage),
                                                        };
                                                    }),
                                            ]),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.title'))
                            ->schema([
                                Forms\Components\Select::make('partner_address_id')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.partner-address'))
                                    ->relationship('partnerAddress', 'name')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: new HtmlString(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.partner-address-hint-tooltip')))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Form $form): Form => AddressResource::form($form))
                                    ->hidden(fn (Forms\Get $get): bool => $get('action') == Enums\RuleAction::PUSH->value),
                                Forms\Components\TextInput::make('delay')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.lead-time'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: new HtmlString(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fields.lead-time-hint-tooltip')))
                                    ->integer()
                                    ->minValue(0),

                                Forms\Components\Fieldset::make(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fieldsets.applicability.title'))
                                    ->schema([
                                        Forms\Components\Select::make('route_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/rule.form.sections.settings.fieldsets.applicability.fields.route'))
                                            ->relationship(
                                                'route',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name . ($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->hiddenOn([ManageRules::class, RulesRelationManager::class]),
                                        Forms\Components\Select::make('company_id')
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
                Tables\Columns\TextColumn::make('action')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.action'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.source-location'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.destination-location'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('route.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.route'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
                Tables\Filters\SelectFilter::make('action')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.action'))
                    ->options(Enums\RuleAction::class),
                Tables\Filters\SelectFilter::make('source_location_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.source-location'))
                    ->relationship('sourceLocation', 'full_name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('destination_location_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.destination-location'))
                    ->relationship('destinationLocation', 'full_name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('route_id')
                    ->label(__('inventories::filament/clusters/configurations/resources/rule.table.filters.route'))
                    ->relationship('route', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.edit.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.restore.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.restore.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/rule.table.actions.force-delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/rule.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.restore.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('inventories::filament/clusters/configurations/resources/rule.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.title'))
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
                                    Enums\RuleAction::PULL      => new HtmlString($pullMessage),
                                    Enums\RuleAction::PUSH      => new HtmlString($pushMessage),
                                    Enums\RuleAction::PULL_PUSH => new HtmlString($pullMessage.'</br></br>'.$pushMessage),
                                };
                            })
                            ->schema([
                                Infolists\Components\Group::make()
                                    ->schema([
                                        Infolists\Components\Group::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.name'))
                                                    ->icon('heroicon-o-document-text')
                                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                                    ->weight(FontWeight::Bold),

                                                Infolists\Components\TextEntry::make('action')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.action'))
                                                    ->icon('heroicon-o-arrows-right-left')
                                                    ->badge(),

                                                Infolists\Components\TextEntry::make('operationType.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.operation-type'))
                                                    ->icon('heroicon-o-briefcase'),

                                                Infolists\Components\TextEntry::make('sourceLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.source-location'))
                                                    ->icon('heroicon-o-map-pin'),

                                                Infolists\Components\TextEntry::make('destinationLocation.full_name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.destination-location'))
                                                    ->icon('heroicon-o-map-pin'),
                                            ]),

                                        Infolists\Components\Group::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('route.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.route'))
                                                    ->icon('heroicon-o-globe-alt'),

                                                Infolists\Components\TextEntry::make('company.name')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.company'))
                                                    ->icon('heroicon-o-building-office'),

                                                Infolists\Components\TextEntry::make('partner_address_id')
                                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.partner-address'))
                                                    ->icon('heroicon-o-user-group')
                                                    ->getStateUsing(fn ($record) => $record->partnerAddress?->name)
                                                    ->placeholder('—'),

                                                Infolists\Components\TextEntry::make('delay')
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

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.entries.created-by'))
                                    ->icon('heroicon-m-user'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('inventories::filament/clusters/configurations/resources/rule.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRules::route('/'),
            'create' => Pages\CreateRule::route('/create'),
            'view'   => Pages\ViewRule::route('/{record}'),
            'edit'   => Pages\EditRule::route('/{record}/edit'),
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
