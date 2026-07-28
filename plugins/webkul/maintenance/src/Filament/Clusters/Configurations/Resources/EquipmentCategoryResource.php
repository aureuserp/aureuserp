<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Maintenance\Filament\Clusters\Configurations;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages\CreateEquipmentCategory;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages\EditEquipmentCategory;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages\ListEquipmentCategories;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages\ViewEquipmentCategory;
use Webkul\Maintenance\Models\EquipmentCategory;

class EquipmentCategoryResource extends Resource
{
    protected static ?string $model = EquipmentCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('maintenance::models/equipment-category.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/configurations/resources/equipment-category.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('maintenance::filament/clusters/configurations/resources/equipment-category.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->unique(ignoreRecord: true),

                        Select::make('technician_user_id')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.form.sections.general.fields.technician'))
                            ->relationship('technician', 'name')
                            ->searchable()
                            ->preload()
                            ->default(Auth::id()),

                        Select::make('company_id')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.form.sections.general.fields.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->default(Auth::user()?->default_company_id),

                        Textarea::make('note')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.form.sections.general.fields.note'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('technician.name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.columns.technician'))
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.columns.company'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('technician.name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.groups.technician')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.actions.edit.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.actions.delete.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.bulk-actions.delete.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.bulk-actions.delete.notification.body')),
                    ),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.empty-state.create.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.empty-state.create.notification.body')),
                    ),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('maintenance::filament/clusters/configurations/resources/equipment-category.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.infolist.sections.general.entries.name'))
                            ->placeholder('—'),

                        TextEntry::make('technician.name')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.infolist.sections.general.entries.technician'))
                            ->placeholder('—'),

                        TextEntry::make('company.name')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.infolist.sections.general.entries.company'))
                            ->placeholder('—'),

                        TextEntry::make('note')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.infolist.sections.general.entries.note'))
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEquipmentCategories::route('/'),
            'create' => CreateEquipmentCategory::route('/create'),
            'view'   => ViewEquipmentCategory::route('/{record}'),
            'edit'   => EditEquipmentCategory::route('/{record}/edit'),
        ];
    }
}
