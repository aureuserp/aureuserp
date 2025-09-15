<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasonResource\Pages\ListRefuseReasons;
use Webkul\Recruitment\Models\RefuseReason;

class RefuseReasonResource extends Resource
{
    protected static ?string $model = RefuseReason::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/refuse-reason.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/refuse-reason.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/refuse-reason.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.name-placeholder')),
                Select::make('template')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.template.title'))
                    ->options([
                        'applicant-refuse'         => __('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.template.applicant-refuse'),
                        'applicant-not-interested' => __('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.template.applicant-not-interested'),
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('template')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.template'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.filters.name'))
                            ->icon('heroicon-o-user'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.actions.edit.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.actions.delete.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->reorderable('sort', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.infolist.name')),
                TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.infolist.template')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRefuseReasons::route('/'),
        ];
    }
}
