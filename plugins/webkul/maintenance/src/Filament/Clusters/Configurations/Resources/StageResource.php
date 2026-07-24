<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Webkul\Maintenance\Filament\Clusters\Configurations;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource\Pages\ManageStages;
use Webkul\Maintenance\Models\Stage;

class StageResource extends Resource
{
    protected static ?string $model = Stage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('maintenance::models/stage.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/configurations/resources/stage.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->unique(ignoreRecord: true),

                Toggle::make('done')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.form.fields.done')),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                IconColumn::make('done')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.table.columns.done'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('done')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.table.groups.done')),
                Group::make('created_at')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/stage.table.actions.edit.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/stage.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/stage.table.actions.delete.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/stage.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/configurations/resources/stage.table.bulk-actions.delete.notification.title'))
                                ->body(__('maintenance::filament/clusters/configurations/resources/stage.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('maintenance::filament/clusters/configurations/resources/stage.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('maintenance::filament/clusters/configurations/resources/stage.infolist.sections.general.entries.name')),

                        IconEntry::make('done')
                            ->label(__('maintenance::filament/clusters/configurations/resources/stage.infolist.sections.general.entries.done'))
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStages::route('/'),
        ];
    }
}
