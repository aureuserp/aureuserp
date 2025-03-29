<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasonResource\Pages;
use Webkul\Recruitment\Models\RefuseReason;

class RefuseReasonResource extends Resource
{
    protected static ?string $model = RefuseReason::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';

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

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'createdBy.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('recruitments::filament/clusters/configurations/resources/refuse-reason.global-search.name')       => $record->name ?? '—',
            __('recruitments::filament/clusters/configurations/resources/refuse-reason.global-search.created-by') => $record->createdBy?->name ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.name-placeholder')),
                Forms\Components\Select::make('template')
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
                Tables\Columns\TextColumn::make('id')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('template')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.template'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.filters.name'))
                            ->icon('heroicon-o-user'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.actions.edit.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.actions.edit.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.actions.delete.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/refuse-reason.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.infolist.name')),
                Infolists\Components\TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-briefcase')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.infolist.template')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRefuseReasons::route('/'),
        ];
    }
}
