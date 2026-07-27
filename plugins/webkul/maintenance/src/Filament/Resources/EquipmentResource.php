<?php

namespace Webkul\Maintenance\Filament\Resources;

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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages\CreateEquipment;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages\EditEquipment;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages\ListEquipment;
use Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages\ViewEquipment;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Maintenance\Models\EquipmentCategory;
use Webkul\Security\Traits\HasResourcePermissionQuery;
use Webkul\Support\Enums\NavigationGroup;

class EquipmentResource extends Resource
{
    use HasResourcePermissionQuery;

    protected static ?string $model = Equipment::class;

    protected static ?string $slug = 'maintenance/equipments';

    protected static ?int $navigationSort = -1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('maintenance::models/equipment.title');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Maintenance;
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/resources/equipment.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/resources/equipment.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),

                                Textarea::make('note')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.general.fields.note'))
                                    ->rows(8),
                            ]),

                        Section::make(__('maintenance::filament/resources/equipment.form.sections.product-information.title'))
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.partner'))
                                    ->relationship('partner', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('partner_ref')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.partner-ref'))
                                    ->maxLength(255),

                                TextInput::make('model')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.model'))
                                    ->maxLength(255),

                                TextInput::make('serial_no')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.serial-no'))
                                    ->maxLength(255),

                                DatePicker::make('effective_date')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.effective-date'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('maintenance::filament/resources/equipment.form.sections.product-information.fields.effective-date-hint-tooltip'))
                                    ->native(false)
                                    ->required()
                                    ->default(now()),

                                TextInput::make('cost')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.cost'))
                                    ->numeric()
                                    ->default(0),

                                DatePicker::make('warranty_date')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.product-information.fields.warranty-date'))
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/resources/equipment.form.sections.settings.title'))
                            ->schema([
                                Select::make('category_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.category'))
                                    ->relationship('category', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, mixed $state): void {
                                        $set('technician_user_id', EquipmentCategory::find($state)?->technician_user_id);
                                    }),

                                Select::make('maintenance_team_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.team'))
                                    ->relationship(
                                        'team',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->native(false)
                                    ->searchable()
                                    ->preload(),

                                Select::make('company_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::user()?->default_company_id),

                                Select::make('technician_user_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.technician'))
                                    ->relationship('technician', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload(),

                                Select::make('owner_user_id')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.owner'))
                                    ->relationship('owner', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('location')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.settings.fields.location'))
                                    ->maxLength(255),
                            ]),

                        Section::make(__('maintenance::filament/resources/equipment.form.sections.maintenance.title'))
                            ->schema([
                                TextInput::make('expected_mtbf')
                                    ->label(__('maintenance::filament/resources/equipment.form.sections.maintenance.fields.expected-mtbf'))
                                    ->numeric()
                                    ->suffix(__('maintenance::filament/resources/equipment.form.sections.maintenance.suffixes.days')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('owner.name')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.owner'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('serial_no')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.serial-no'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('technician.name')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.technician'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.category'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.company'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('maintenance::filament/resources/equipment.table.filters.category'))
                    ->relationship('category', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('maintenance_team_id')
                    ->label(__('maintenance::filament/resources/equipment.table.filters.team'))
                    ->relationship('team', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('technician_user_id')
                    ->label(__('maintenance::filament/resources/equipment.table.filters.technician'))
                    ->relationship('technician', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),
            ])
            ->groups([
                TableGroup::make('technician.name')
                    ->label(__('maintenance::filament/resources/equipment.table.groups.technician')),
                TableGroup::make('category.name')
                    ->label(__('maintenance::filament/resources/equipment.table.groups.category')),
                TableGroup::make('owner.name')
                    ->label(__('maintenance::filament/resources/equipment.table.groups.owner')),
                TableGroup::make('partner.name')
                    ->label(__('maintenance::filament/resources/equipment.table.groups.vendor')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/resources/equipment.table.actions.edit.notification.title'))
                            ->body(__('maintenance::filament/resources/equipment.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/resources/equipment.table.actions.restore.notification.title'))
                            ->body(__('maintenance::filament/resources/equipment.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/resources/equipment.table.actions.delete.notification.title'))
                            ->body(__('maintenance::filament/resources/equipment.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Equipment $record): void {
                        try {
                            $record->forceDelete();

                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.success.title'))
                                ->body(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.success.body'))
                                ->send();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.error.title'))
                                ->body(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/resources/equipment.table.bulk-actions.restore.notification.title'))
                                ->body(__('maintenance::filament/resources/equipment.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/resources/equipment.table.bulk-actions.delete.notification.title'))
                                ->body(__('maintenance::filament/resources/equipment.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.success.title'))
                                    ->body(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.error.title'))
                                    ->body(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/resources/equipment.table.empty-state.create.notification.title'))
                            ->body(__('maintenance::filament/resources/equipment.table.empty-state.create.notification.body')),
                    ),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/resources/equipment.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.general.entries.name')),

                                TextEntry::make('note')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.general.entries.note'))
                                    ->placeholder('—'),
                            ]),

                        Section::make(__('maintenance::filament/resources/equipment.infolist.sections.product-information.title'))
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.partner'))
                                    ->placeholder('—'),

                                TextEntry::make('partner_ref')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.partner-ref'))
                                    ->placeholder('—'),

                                TextEntry::make('model')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.model'))
                                    ->placeholder('—'),

                                TextEntry::make('serial_no')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.serial-no'))
                                    ->placeholder('—'),

                                TextEntry::make('effective_date')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.effective-date'))
                                    ->date()
                                    ->placeholder('—'),

                                TextEntry::make('cost')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.cost'))
                                    ->numeric(decimalPlaces: 2)
                                    ->placeholder('—'),

                                TextEntry::make('warranty_date')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.product-information.entries.warranty-date'))
                                    ->date()
                                    ->placeholder('—'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('maintenance::filament/resources/equipment.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('category.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.category'))
                                    ->placeholder('—'),

                                TextEntry::make('team.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.team'))
                                    ->placeholder('—'),

                                TextEntry::make('company.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.company'))
                                    ->placeholder('—'),

                                TextEntry::make('technician.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.technician'))
                                    ->placeholder('—'),

                                TextEntry::make('owner.name')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.owner'))
                                    ->placeholder('—'),

                                TextEntry::make('location')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.settings.entries.location'))
                                    ->placeholder('—'),
                            ]),

                        Section::make(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.title'))
                            ->schema([
                                TextEntry::make('expected_mtbf')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.entries.expected-mtbf'))
                                    ->suffix(' '.__('maintenance::filament/resources/equipment.infolist.sections.maintenance.suffixes.days'))
                                    ->placeholder('0'),

                                TextEntry::make('maintenance_count')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.entries.maintenance-count'))
                                    ->placeholder('0'),

                                TextEntry::make('maintenance_open_count')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.entries.maintenance-open-count'))
                                    ->placeholder('0'),

                                TextEntry::make('assigned_at')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.entries.assigned-at'))
                                    ->date()
                                    ->placeholder('—'),

                                TextEntry::make('scraped_at')
                                    ->label(__('maintenance::filament/resources/equipment.infolist.sections.maintenance.entries.scraped-at'))
                                    ->date()
                                    ->placeholder('—'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEquipment::route('/'),
            'create' => CreateEquipment::route('/create'),
            'view'   => ViewEquipment::route('/{record}'),
            'edit'   => EditEquipment::route('/{record}/edit'),
        ];
    }
}
