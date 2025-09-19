@props([
    'applyAction',
    'columns' => null,
    'tableColumns' => null,
    'hasReorderableColumns',
    'hasToggleableColumns',
    'headingTag' => 'h3',
    'reorderAnimationDuration' => 300,
    'applyTableColumnManager',
    'repeaterKey' => null,
])

@php
    use Filament\Support\Enums\GridDirection;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<div class="p-4 fi-ta-col-manager">
    <div
        x-data="(() => {
            return {
                error: undefined,
                isLoading: false,
                columns: @js($tableColumns),
                isLive: {{ $applyAction->isVisible() ? 'false' : 'true' }},
                repeaterKey: @js($repeaterKey),

                init() {
                    if (!this.columns || this.columns.length === 0) {
                        this.columns = []
                        return
                    }
                },

                get groupedColumns() {
                    const groupedColumns = {}
                    this.columns.filter(c => c.type === 'group').forEach(c => {
                        groupedColumns[c.name] = this.calculateGroupedColumns(c)
                    })
                    return groupedColumns
                },

                calculateGroupedColumns(group) {
                    const visibleChildren = group?.columns?.filter(c => !c.isHidden) ?? []
                    if (visibleChildren.length === 0) {
                        return { hidden: true, checked: false, disabled: false, indeterminate: false }
                    }

                    const toggleableChildren = group.columns.filter(c => !c.isHidden && c.isToggleable !== false)
                    if (toggleableChildren.length === 0) {
                        return { checked: true, disabled: true, indeterminate: false }
                    }

                    const toggledChildren = toggleableChildren.filter(c => c.isToggled).length
                    const nonToggleableChildren = group.columns.filter(c => !c.isHidden && c.isToggleable === false)

                    if (toggledChildren === 0 && nonToggleableChildren.length > 0) {
                        return { checked: true, disabled: false, indeterminate: true }
                    }

                    if (toggledChildren === 0) {
                        return { checked: false, disabled: false, indeterminate: false }
                    }

                    if (toggledChildren === toggleableChildren.length) {
                        return { checked: true, disabled: false, indeterminate: false }
                    }

                    return { checked: true, disabled: false, indeterminate: true }
                },

                getColumn(name, groupName = null) {
                    if (groupName) {
                        const group = this.columns.find(g => g.type === 'group' && g.name === groupName)
                        return group?.columns?.find(c => c.name === name)
                    }
                    return this.columns.find(c => c.name === name)
                },

                toggleGroup(groupName) {
                    const group = this.columns.find(g => g.type === 'group' && g.name === groupName)
                    if (!group?.columns) return

                    const groupedColumns = this.calculateGroupedColumns(group)
                    if (groupedColumns.disabled) return

                    const toggleableChildren = group.columns.filter(c => c.isToggleable !== false)
                    const anyChildOn = toggleableChildren.some(c => c.isToggled)
                    const newValue = groupedColumns.indeterminate ? true : !anyChildOn

                    group.columns.filter(c => c.isToggleable !== false).forEach(c => c.isToggled = newValue)
                    this.columns = [...this.columns]

                    if (this.isLive) this.applyTableColumnManager()
                },

                toggleColumn(name, groupName = null) {
                    const column = this.getColumn(name, groupName)
                    if (!column || column.isToggleable === false) return

                    column.isToggled = !column.isToggled
                    this.columns = [...this.columns]

                    if (this.isLive) this.applyTableColumnManager()
                },

                reorderColumns(sortedIds) {
                    const newOrder = sortedIds.map(id => id.split('::'))
                    this.reorderTopLevel(newOrder)
                    if (this.isLive) this.applyTableColumnManager()
                },

                reorderGroupColumns(sortedIds, groupName) {
                    const group = this.columns.find(c => c.type === 'group' && c.name === groupName)
                    if (!group) return

                    const newOrder = sortedIds.map(id => id.split('::'))
                    const reordered = []
                    newOrder.forEach(([type, name]) => {
                        const item = group.columns.find(c => c.name === name)
                        if (item) reordered.push(item)
                    })

                    group.columns = reordered
                    this.columns = [...this.columns]

                    if (this.isLive) this.applyTableColumnManager()
                },

                reorderTopLevel(newOrder) {
                    const cloned = this.columns
                    const reordered = []
                    newOrder.forEach(([type, name]) => {
                        const item = cloned.find(c => {
                            if (type === 'group') return c.type === 'group' && c.name === name
                            else if (type === 'column') return c.type !== 'group' && c.name === name
                            return false
                        })
                        if (item) reordered.push(item)
                    })
                    this.columns = reordered
                },

                async applyTableColumnManager() {
                    this.isLoading = true;
                    try {
                        await this.$wire.call('applyRepeaterColumnManager', this.repeaterKey, this.columns);
                    } catch (error) {
                        this.error = error;
                        console.error('Column manager error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },
            }
        })()"
        class="fi-ta-col-manager-ctn"
    >
        <div class="fi-ta-col-manager-header">
            <{{ $headingTag }} class="fi-ta-col-manager-heading">
                {{ __('filament-tables::table.column_manager.heading') }}
            </{{ $headingTag }}>

            <div>
                <x-filament::link
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes(
                            new ComponentAttributeBag([
                                'color' => 'danger',
                                'tag' => 'button',
                                'wire:click' => 'resetRepeaterColumnManager(\'' . $repeaterKey . '\')',
                                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                                'wire:target' => 'resetRepeaterColumnManager',
                            ])
                        )
                    "
                >
                    {{ __('filament-tables::table.column_manager.actions.reset.label') }}
                </x-filament::link>
            </div>
        </div>

        <div
            @if ($hasReorderableColumns)
                x-sortable
                x-on:end.stop="reorderColumns($event.target.sortable.toArray())"
                data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
            @endif
            {{
                (new ComponentAttributeBag)
                    ->grid($columns, GridDirection::Column)
                    ->class(['fi-ta-col-manager-items'])
            }}
        >
            <template
                x-for="(column, index) in columns.filter((column) => ! column.isHidden && column.label)"
                x-bind:key="(column.type === 'group' ? 'group::' : 'column::') + column.name + '_' + index"
            >
                <div
                    @if ($hasReorderableColumns)
                        x-bind:x-sortable-item="column.type === 'group' ? 'group::' + column.name : 'column::' + column.name"
                    @endif
                >
                    <template x-if="column.type === 'group'">
                        <div class="fi-ta-col-manager-group">
                            <div class="fi-ta-col-manager-item">
                                <label class="fi-ta-col-manager-label">
                                    @if ($hasToggleableColumns)
                                        <input
                                            type="checkbox"
                                            class="fi-checkbox-input fi-valid"
                                            x-bind:id="'group-' + column.name"
                                            x-bind:checked="(groupedColumns[column.name] || {}).checked || false"
                                            x-bind:disabled="(groupedColumns[column.name] || {}).disabled || false"
                                            x-effect="$el.indeterminate = (groupedColumns[column.name] || {}).indeterminate || false"
                                            x-on:change="toggleGroup(column.name)"
                                        />
                                    @endif

                                    <span x-text="column.label"></span>
                                </label>

                                @if ($hasReorderableColumns)
                                    <button
                                        x-sortable-handle
                                        x-on:click.stop
                                        class="fi-ta-col-manager-reorder-handle fi-icon-btn"
                                        type="button"
                                    >
                                        {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: \Filament\Tables\View\TablesIconAlias::REORDER_HANDLE) }}
                                    </button>
                                @endif
                            </div>
                            <div
                                @if ($hasReorderableColumns)
                                    x-sortable
                                    x-on:end.stop="reorderGroupColumns($event.target.sortable.toArray(), column.name)"
                                    data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
                                @endif
                                class="fi-ta-col-manager-group-items"
                            >
                                <template
                                    x-for="
                                        (groupColumn, index) in
                                            column.columns.filter((column) => ! column.isHidden && column.label)
                                    "
                                    x-bind:key="'column::' + groupColumn.name + '_' + index"
                                >
                                    <div
                                        @if ($hasReorderableColumns)
                                            x-bind:x-sortable-item="'column::' + groupColumn.name"
                                        @endif
                                    >
                                        <div class="fi-ta-col-manager-item">
                                            <label
                                                class="fi-ta-col-manager-label"
                                            >
                                                @if ($hasToggleableColumns)
                                                    <input
                                                        type="checkbox"
                                                        class="fi-checkbox-input fi-valid"
                                                        x-bind:id="'column-' + groupColumn.name.replace('.', '-')"
                                                        x-bind:checked="(getColumn(groupColumn.name, column.name) || {}).isToggled || false"
                                                        x-bind:disabled="(getColumn(groupColumn.name, column.name) || {}).isToggleable === false"
                                                        x-on:change="toggleColumn(groupColumn.name, column.name)"
                                                    />
                                                @endif

                                                <span
                                                    x-text="groupColumn.label"
                                                ></span>
                                            </label>

                                            @if ($hasReorderableColumns)
                                                <button
                                                    x-sortable-handle
                                                    x-on:click.stop
                                                    class="fi-ta-col-manager-reorder-handle fi-icon-btn"
                                                    type="button"
                                                >
                                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: \Filament\Tables\View\TablesIconAlias::REORDER_HANDLE) }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="column.type !== 'group'">
                        <div class="fi-ta-col-manager-item">
                            <label class="fi-ta-col-manager-label">
                                @if ($hasToggleableColumns)
                                    <input
                                        type="checkbox"
                                        class="fi-checkbox-input fi-valid"
                                        x-bind:id="'column-' + column.name.replace('.', '-')"
                                        x-bind:checked="(getColumn(column.name, null) || {}).isToggled || false"
                                        x-bind:disabled="(getColumn(column.name, null) || {}).isToggleable === false"
                                        x-on:change="toggleColumn(column.name)"
                                    />
                                @endif

                                <span x-text="column.label"></span>
                            </label>

                            @if ($hasReorderableColumns)
                                <button
                                    x-sortable-handle
                                    x-on:click.stop
                                    class="fi-ta-col-manager-reorder-handle fi-icon-btn"
                                    type="button"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: \Filament\Tables\View\TablesIconAlias::REORDER_HANDLE) }}
                                </button>
                            @endif
                        </div>
                    </template>
                </div>
            </template>
        </div>

        @if ($applyAction->isVisible())
            <div class="fi-ta-col-manager-apply-action-ctn">
                {{ $applyAction }}
            </div>
        @endif
    </div>
</div>