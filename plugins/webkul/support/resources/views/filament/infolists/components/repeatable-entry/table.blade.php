@php
    use Filament\Support\Enums\Alignment;
    use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn;

    $items = $getItems();
    $tableColumns = $getTableColumns();
    $extraActions = $getExtraItemActions();
    $hasExtraActions = ! empty($extraActions);
    $hasColumnManager = $hasColumnManager();

    $attributes = $getExtraAttributeBag()
        ->class(['fi-fo-table-repeater', 'fi-compact']);

    $hasSummary = $hasAnySummarizers();
    $hasResizableColumns = collect($tableColumns)->contains(fn (TableColumn $column) => $column->isResizable());

    $resizableColumnConfig = collect($tableColumns)->mapWithKeys(function (TableColumn $column) {
        return [$column->getName() => [
            'isResizable' => $column->isResizable(),
            'minWidth'    => $column->getMinWidth(),
            'maxWidth'    => $column->getMaxWidth(),
            'width'       => $column->getWidth(),
        ]];
    })->toArray();
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
        <table
            class="fi-absolute-positioning-context overflow-hidden"
            @if ($hasResizableColumns)
                x-data="{
                    columns: @js($resizableColumnConfig),
                    columnWidths: {},
                    resizing: null,
                    startX: 0,
                    startWidth: 0,

                    init() {
                        Object.keys(this.columns).forEach(name => {
                            const col = this.columns[name];
                            if (col.width) {
                                this.columnWidths[name] = parseInt(col.width, 10) || null;
                            }
                        });
                    },

                    getColumnStyle(name) {
                        const width = this.columnWidths[name];
                        const col = this.columns[name];

                        if (! width && col.width) {
                            return 'width: ' + col.width;
                        }

                        if (width) {
                            return 'width: ' + width + 'px';
                        }

                        return '';
                    },

                    startResize(event, columnName) {
                        const th = event.target.closest('th');
                        if (! th) return;

                        this.resizing = columnName;
                        this.startX = event.pageX;
                        this.startWidth = th.offsetWidth;

                        document.body.style.cursor = 'col-resize';
                        document.body.style.userSelect = 'none';

                        const onMouseMove = (e) => {
                            if (! this.resizing) return;

                            const diff = e.pageX - this.startX;
                            let newWidth = this.startWidth + diff;
                            const col = this.columns[this.resizing];

                            if (col.minWidth) {
                                const min = parseInt(col.minWidth, 10);
                                if (min && newWidth < min) newWidth = min;
                            }

                            if (col.maxWidth) {
                                const max = parseInt(col.maxWidth, 10);
                                if (max && newWidth > max) newWidth = max;
                            }

                            if (newWidth < 50) newWidth = 50;

                            this.columnWidths[this.resizing] = newWidth;
                        };

                        const onMouseUp = () => {
                            this.resizing = null;
                            document.body.style.cursor = '';
                            document.body.style.userSelect = '';
                            document.removeEventListener('mousemove', onMouseMove);
                            document.removeEventListener('mouseup', onMouseUp);
                        };

                        document.addEventListener('mousemove', onMouseMove);
                        document.addEventListener('mouseup', onMouseUp);
                    },

                    resetColumnWidth(name) {
                        const col = this.columns[name];
                        if (col.width) {
                            this.columnWidths[name] = parseInt(col.width, 10) || null;
                        } else {
                            delete this.columnWidths[name];
                        }
                    },
                }"
            @endif
        >
            <thead>
                <tr>
                    @foreach ($tableColumns as $column)
                        @php
                            $columnName = $column->getName();
                            $columnWidth = $column->getWidth();
                            $columnAlignment = $column->getAlignment();
                            $isResizable = $column->isResizable();

                            $alignmentClass = $columnAlignment instanceof \Filament\Support\Enums\Alignment
                                ? 'fi-align-' . $columnAlignment->value
                                : $columnAlignment;
                        @endphp

                        <th
                            class="{{ \Illuminate\Support\Arr::toCssClasses([
                                'fi-wrapped' => $column->canHeaderWrap(),
                                'fi-resizable-column' => $isResizable,
                                $alignmentClass,
                            ]) }}"
                            @if ($hasResizableColumns && $isResizable)
                                x-bind:style="getColumnStyle('{{ $columnName }}')"
                                data-column="{{ $columnName }}"
                            @else
                                @style([
                                    'width:' . $columnWidth => filled($columnWidth),
                                ])
                            @endif
                        >
                            <div class="fi-fo-table-repeater-header-content">
                                @if (! $column->isHeaderLabelHidden())
                                    {{ $column->getLabel() }}
                                @else
                                    <span class="fi-sr-only">{{ $column->getLabel() }}</span>
                                @endif
                            </div>

                            @if ($isResizable && $hasResizableColumns)
                                <div
                                    class="fi-fo-table-repeater-resize-handle"
                                    x-on:mousedown.prevent="startResize($event, '{{ $columnName }}')"
                                    x-on:dblclick.prevent="resetColumnWidth('{{ $columnName }}')"
                                ></div>
                            @endif
                        </th>
                    @endforeach

                    @if ($hasColumnManager)
                        <th
                            class="fi-fo-table-repeater-empty-header-cell text-center align-middle"
                            style="width: 75px; white-space: nowrap;"
                        >
                            <x-filament::dropdown
                                shift
                                placement="bottom-end"
                                :max-height="$getColumnManagerMaxHeight()"
                                :width="$getColumnManagerWidth()"
                                :wire:key="$getId() . '.table.column-manager.' . $getStatePath()"
                                class="fi-ta-col-manager-dropdown inline-block"
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
                                <div>
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

            @if ($hasSummary)
                <tfoot class="fi-ta-row fi-ta-summary-row fi-striped">
                    <tr>
                        @foreach ($tableColumns as $tableColumn)
                            <td class="fi-ta-cell px-3 py-3 font-semibold">
                                @if ($tableColumn->hasSummarizer())
                                    {{ $getSummaryForColumn($tableColumn->getName()) }}
                                @endif
                            </td>
                        @endforeach

                        @if ($hasColumnManager)
                            <td></td>
                        @endif
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
@endif
