@php
    $items = $getItems();
    $tableColumns = $getTableColumns();
    $extraActions = $getExtraItemActions();
    $hasExtraActions = ! empty($extraActions);
    $hasColumnManager = $hasColumnManager();

    $attributes = $getExtraAttributeBag()
        ->class(['fi-fo-table-repeater', 'fi-compact']);
@endphp

@if (empty($items))
    @php
        $tooltip = $getEmptyTooltip();

        $attributes = $attributes->merge([
            'x-tooltip' => filled($tooltip)
                ? '{content: ' . Js::from($tooltip) . ', theme: $store.theme, allowHTML: ' . Js::from($tooltip instanceof \Illuminate\Contracts\Support\Htmlable) . '}'
                : null,
        ], escape: false);
    @endphp

    <div {{ $attributes }}>
        @if (filled($placeholder = $getPlaceholder()))
            <p class="fi-in-placeholder">{{ $placeholder }}</p>
        @endif
    </div>
@else
    <div {{ $attributes }}>
        <table class="fi-absolute-positioning-context">
            <thead>
                <tr>
                    @foreach ($tableColumns as $column)
                        @php
                            $columnWidth = $column->getWidth();
                            $columnAlignment = $column->getAlignment();

                            $alignmentClass = $columnAlignment instanceof \Filament\Support\Enums\Alignment
                                ? 'fi-align-' . $columnAlignment->value
                                : $columnAlignment;
                        @endphp

                        <th
                            class="{{ \Illuminate\Support\Arr::toCssClasses([
                                'fi-wrapped' => $column->canHeaderWrap(),
                                $alignmentClass,
                            ]) }}"
                            @style([
                                'width:' . $columnWidth => filled($columnWidth),
                            ])
                        >
                            @if (! $column->isHeaderLabelHidden())
                                {{ $column->getLabel() }}
                            @else
                                <span class="fi-sr-only">{{ $column->getLabel() }}</span>
                            @endif
                        </th>
                    @endforeach

                    @if ($hasColumnManager)
                        <th
                            class="text-center align-middle fi-fo-table-repeater-empty-header-cell"
                            style="width: 75px; white-space: nowrap;"
                        >
                            <x-filament::dropdown
                                shift
                                placement="bottom-end"
                                :max-height="$getColumnManagerMaxHeight()"
                                :width="$getColumnManagerWidth()"
                                :wire:key="$getId() . '.table.column-manager.' . $getStatePath()"
                                class="inline-block fi-ta-col-manager-dropdown"
                                x-data="{ open: false }"
                                x-on:click="$dispatch('toggle-dropdown')"
                                x-on:toggle-dropdown="open = !open"
                            >
                                <x-slot name="trigger">
                                    {!! $getColumnManagerTriggerAction()->toHtml() !!}
                                </x-slot>

                                <x-support::column-manager
                                    heading-tag="h2"
                                    :apply-action="$getColumnManagerApplyAction()"
                                    :table-columns="$getMappedColumns()"
                                    :columns="$getColumnManagerColumns()"
                                    :has-reorderable-columns="false"
                                    :has-toggleable-columns="$hasToggleableColumns()"
                                    :reorder-animation-duration="300"
                                    :repeater-key="$getStatePath()"
                                />
                            </x-filament::dropdown>
                        </th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $index => $item)
                    <tr>
                       @php
                            $visibleColumns = collect($tableColumns)
                                ->mapWithKeys(fn ($col) => [$col->getName() => $col]);
                        @endphp

                        @foreach ($item->getComponents(withHidden: true) as $component)
                            @continue(! ($component instanceof \Filament\Schemas\Components\Component))
                            
                            @continue(! $visibleColumns->has($component->getName()))

                            <td>
                                <div style="min-width: max-content; padding: 6px 2px;">
                                    {!! $component->toHtml() !!}
                                </div>
                            </td>
                        @endforeach

                        @if ($hasExtraActions)
                            <td>
                                <div
                                    class="flex items-center justify-center gap-2"
                                    style="min-width: max-content; padding: 6px 2px;"
                                >
                                    @foreach ($extraActions as $action)
                                        @php $action = $action(['item' => $index]); @endphp
                                        <div x-on:click.stop>
                                            {!! $action->toHtml() !!}
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
