<?php

namespace Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Pages\ManageCategories;
use Webkul\Blog\Models\Category;
use Webkul\Website\Filament\Admin\Clusters\Configurations;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $cluster = Configurations::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('blogs::filament/admin/clusters/configurations/resources/category.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('blogs::filament/admin/clusters/configurations/resources/category.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('blogs::filament/admin/clusters/configurations/resources/category.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->placeholder(__('blogs::filament/admin/clusters/configurations/resources/category.form.fields.name-placeholder'))
                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                    ->afterStateUpdated(fn(string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(255)
                    ->unique(Category::class, 'slug', ignoreRecord: true),
                TextInput::make('sub_title')
                    ->label(__('blogs::filament/admin/clusters/configurations/resources/category.form.fields.sub-title'))
                    ->maxLength(255),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('blogs::filament/admin/clusters/configurations/resources/category.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('blogs::filament/admin/clusters/configurations/resources/category.table.columns.created-at'))
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.edit.notification.title'))
                            ->body(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.restore.notification.title'))
                            ->body(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.delete.notification.title'))
                            ->body(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Category $record) {
                        try {
                            $record->forceDelete();

                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.force-delete.notification.success.title'))
                                ->body(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.force-delete.notification.success.body'))
                                ->send();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.force-delete.notification.error.title'))
                                ->body(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.force-delete.notification.error.body'))
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
                                ->title(__('blogs::filament/admin/clusters/configurations/resources/category.table.bulk-actions.restore.notification.title'))
                                ->body(__('blogs::filament/admin/clusters/configurations/resources/category.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/clusters/configurations/resources/category.table.bulk-actions.delete.notification.title'))
                                ->body(__('blogs::filament/admin/clusters/configurations/resources/category.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.force-delete.notification.success.title'))
                                    ->body(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.force-delete.notification.error.title'))
                                    ->body(__('blogs::filament/admin/clusters/configurations/resources/category.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
