<?php

namespace Webkul\TableViews\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\Width;
use Guava\IconPicker\Forms\Components\IconPicker;
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
                $tableView = TableView::find($arguments['view_key']);

                $tableViewFavorite = TableViewFavorite::query()
                    ->where('user_id', filament()->auth()->id())
                    ->where('view_type', 'saved')
                    ->where('view_key', $tableView->view_key)
                    ->where('filterable_type', $tableView->filterable_type)
                    ->first();

                return [
                    'name'        => $tableView->name,
                    'color'       => $tableView->color,
                    'icon'        => $tableView->icon,
                    'is_favorite' => $tableViewFavorite?->is_favorite ?? false,
                    'is_public'   => $tableView->is_public,
                ];
            })
            ->schema([
                TextInput::make('name')
                    ->label(__('table-views::filament/actions/edit-view.form.name'))
                    ->autofocus()
                    ->required(),
                IconPicker::make('icon')
                    ->label(__('table-views::filament/actions/edit-view.form.icon'))
                    ->sets(['heroicons'])
                    ->columns(4)
                    ->gridSearchResults()
                    ->iconsSearchResults(),
                Toggle::make('is_favorite')
                    ->label(__('table-views::filament/actions/edit-view.form.add-to-favorites'))
                    ->helperText(__('table-views::filament/actions/edit-view.form.add-to-favorites-help')),
                Toggle::make('is_public')
                    ->label(__('table-views::filament/actions/edit-view.form.make-public'))
                    ->helperText(__('table-views::filament/actions/edit-view.form.make-public-help')),
            ])->action(function (array $arguments): void {
                $tableView = TableView::find($arguments['view_key']);

                $this->process(function (array $data) use ($tableView): TableView {
                    $tableView->fill($data);
                    $tableView->save();

                    TableViewFavorite::updateOrCreate(
                        [
                            'view_type'       => 'saved',
                            'view_key'        => $tableView->id,
                            'filterable_type' => $tableView->filterable_type,
                            'user_id'         => filament()->auth()->id(),
                        ], [
                            'is_favorite' => $data['is_favorite'],
                        ]
                    );

                    return $tableView;
                });

                $this->record($tableView);

                $this->success();
            })
            ->label(__('table-views::filament/actions/edit-view.form.modal.title'))
            ->successNotificationTitle(__('table-views::filament/actions/edit-view.form.notification.created'))
            ->icon('heroicon-s-pencil-square')
            ->modalHeading(__('table-views::filament/actions/edit-view.form.modal.title'))
            ->modalWidth(Width::Medium);
    }
}
