<?php

namespace Webkul\TableViews\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms;
use Filament\Support\Enums\MaxWidth;
use Webkul\TableViews\Models\TableView;
use Webkul\TableViews\Models\TableViewFavorite;

class EditViewAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'table_views.update.action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->model(TableView::class)
            ->fillForm(function (array $arguments): array {
                $tableViewFavorite = TableViewFavorite::query()
                    ->where('user_id', auth()->id())
                    ->where('view_type', 'saved')
                    ->where('view_key', $arguments['view_model']['id'])
                    ->where('filterable_type', $arguments['view_model']['filterable_type'])
                    ->first();

                return [
                    'name'        => $arguments['view_model']['name'],
                    'color'       => $arguments['view_model']['color'],
                    'icon'        => $arguments['view_model']['icon'],
                    'is_favorite' => $tableViewFavorite?->is_favorite ?? false,
                    'is_public'   => $arguments['view_model']['is_public'],
                ];
            })
            ->form([
                Forms\Components\TextInput::make('name')
                    ->label(__('table-views::filament/actions/edit-view.form.name'))
                    ->autofocus()
                    ->required(),
                \Guava\FilamentIconPicker\Forms\IconPicker::make('icon')
                    ->label(__('table-views::filament/actions/edit-view.form.icon'))
                    ->sets(['heroicons'])
                    ->columns(4)
                    ->preload()
                    ->optionsLimit(50),
                Forms\Components\Toggle::make('is_favorite')
                    ->label(__('table-views::filament/actions/edit-view.form.add-to-favorites'))
                    ->helperText(__('table-views::filament/actions/edit-view.form.add-to-favorites-help')),
                Forms\Components\Toggle::make('is_public')
                    ->label(__('table-views::filament/actions/edit-view.form.make-public'))
                    ->helperText(__('table-views::filament/actions/edit-view.form.make-public-help')),
            ])->action(function (array $arguments): void {
                TableView::find($arguments['view_model']['id'])->update($arguments['view_model']);

                $record = $this->process(function (array $data) use ($arguments): TableView {
                    $record = TableView::find($arguments['view_model']['id']);
                    $record->fill($data);

                    $record->save();

                    TableViewFavorite::updateOrCreate(
                        [
                            'view_type'       => 'saved',
                            'view_key'        => $arguments['view_model']['id'],
                            'filterable_type' => $record->filterable_type,
                            'user_id'         => auth()->id(),
                        ], [
                            'is_favorite' => $data['is_favorite'],
                        ]
                    );

                    return $record;
                });

                $this->record($record);

                $this->success();
            })
            ->label(__('table-views::filament/actions/edit-view.form.modal.title'))
            ->successNotificationTitle(__('table-views::filament/actions/edit-view.form.notification.created'))
            ->icon('heroicon-s-pencil-square')
            ->modalHeading(__('table-views::filament/actions/edit-view.form.modal.title'))
            ->modalWidth(MaxWidth::Medium);
    }
}
