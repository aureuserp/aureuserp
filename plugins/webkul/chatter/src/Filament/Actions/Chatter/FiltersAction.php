<?php

namespace Webkul\Chatter\Filament\Actions\Chatter;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;

class FiltersAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'filters.action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->hiddenLabel()
            ->icon('heroicon-o-funnel')
            ->color('gray')
            ->outlined()
            ->badge(function ($livewire) {
                try {
                    return method_exists($livewire, 'getActiveFilters')
                        ? (count($livewire->getActiveFilters()) ?: null)
                        : null;
                } catch (\Throwable $e) {
                    return null;
                }
            })
            ->slideOver(false)
            ->tooltip(__('chatter::filament/resources/actions/chatter/filters-action.tooltip'))
            ->mountUsing(function ($livewire, $arguments, $form, $schema) {
                $schema->fill([
                    'search'     => $livewire->search ?? '',
                    'filterType' => $livewire->filterType ?? 'all',
                    'dateRange'  => $livewire->dateRange ?? null,
                    'sortBy'     => $livewire->sortBy ?? 'created_at_desc',
                    'pinnedOnly' => (bool) ($livewire->pinnedOnly ?? false),
                ]);
            })
            ->schema([
                Group::make()
                    ->schema([
                        TextInput::make('search')
                            ->label(__('chatter::filament/resources/actions/chatter/filters-action.fields.search'))
                            ->placeholder(__('chatter::filament/resources/actions/chatter/filters-action.fields.search-placeholder')),
                        Select::make('filterType')
                            ->label(__('chatter::filament/resources/actions/chatter/filters-action.fields.type'))
                            ->options([
                                'all'          => __('chatter::filament/resources/actions/chatter/filters-action.type-options.all'),
                                'note'         => __('chatter::filament/resources/actions/chatter/filters-action.type-options.note'),
                                'comment'      => __('chatter::filament/resources/actions/chatter/filters-action.type-options.comment'),
                                'notification' => __('chatter::filament/resources/actions/chatter/filters-action.type-options.notification'),
                                'activity'     => __('chatter::filament/resources/actions/chatter/filters-action.type-options.activity'),
                            ])
                            ->native(false),
                        Select::make('dateRange')
                            ->label(__('chatter::filament/resources/actions/chatter/filters-action.fields.date'))
                            ->options([
                                ''          => __('chatter::filament/resources/actions/chatter/filters-action.date-options.'),
                                'today'     => __('chatter::filament/resources/actions/chatter/filters-action.date-options.today'),
                                'yesterday' => __('chatter::filament/resources/actions/chatter/filters-action.date-options.yesterday'),
                                'week'      => __('chatter::filament/resources/actions/chatter/filters-action.date-options.week'),
                                'month'     => __('chatter::filament/resources/actions/chatter/filters-action.date-options.month'),
                                'quarter'   => __('chatter::filament/resources/actions/chatter/filters-action.date-options.quarter'),
                                'year'      => __('chatter::filament/resources/actions/chatter/filters-action.date-options.year'),
                            ])
                            ->native(false),
                        Select::make('sortBy')
                            ->label(__('chatter::filament/resources/actions/chatter/filters-action.fields.sort-by'))
                            ->options([
                                'created_at_desc' => __('chatter::filament/resources/actions/chatter/filters-action.sort-options.created_at_desc'),
                                'created_at_asc'  => __('chatter::filament/resources/actions/chatter/filters-action.sort-options.created_at_asc'),
                                'updated_at_desc' => __('chatter::filament/resources/actions/chatter/filters-action.sort-options.updated_at_desc'),
                                'priority'        => __('chatter::filament/resources/actions/chatter/filters-action.sort-options.priority'),
                            ])
                            ->native(false),
                        Toggle::make('pinnedOnly')
                            ->label(__('chatter::filament/resources/actions/chatter/filters-action.fields.pinned-only')),
                    ])
                    ->columns(2),
            ])
            ->modalSubmitAction(function ($action) {
                $action->label(__('chatter::filament/resources/actions/chatter/filters-action.actions.apply'))->icon('heroicon-m-check');
            })
            ->action(function (array $data, $livewire) {
                $livewire->search = (string) ($data['search'] ?? '');
                $livewire->filterType = (string) ($data['filterType'] ?? 'all');
                $livewire->dateRange = $data['dateRange'] !== '' ? ($data['dateRange'] ?? null) : null;
                $livewire->sortBy = (string) ($data['sortBy'] ?? 'created_at_desc');
                $livewire->pinnedOnly = (bool) ($data['pinnedOnly'] ?? false);

                if (method_exists($livewire, 'dispatch')) {
                    $livewire->dispatch('chatter.refresh');
                }
            });
    }
}
