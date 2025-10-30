@php
    use Filament\Actions\Action;
    use Filament\Actions\ActionGroup;
    use Filament\Support\Enums\Alignment;
    use Illuminate\View\ComponentAttributeBag;

    $items = $getItems();

    $addAction = $getAction($getAddActionName());
    $addActionAlignment = $getAddActionAlignment();
    $addBetweenAction = $getAction($getAddBetweenActionName());
    $cloneAction = $getAction($getCloneActionName());
    $deleteAction = $getAction($getDeleteActionName());
    $moveDownAction = $getAction($getMoveDownActionName());
    $moveUpAction = $getAction($getMoveUpActionName());
    $reorderAction = $getAction($getReorderActionName());
    $extraItemActions = $getExtraItemActions();

    $isAddable = $isAddable();
    $isCloneable = $isCloneable();
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

    $key = $getKey();
    $statePath = $getStatePath();

    $tableColumns = $getTableColumns();
    $isCompact = $isCompact();

    $hasColumnManagerDropdown = $hasColumnManager();
    $columnManagerApplyAction = $getColumnManagerApplyAction();
    $columnManagerTriggerAction = $getColumnManagerTriggerAction();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        {{ 
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-table-repeater',
                    'fi-compact' => $isCompact,
                ]) 
        }}
    >
        @if (count($items))
            <table class="fi-absolute-positioning-context">
                <thead>
                    <tr>
                        @if (
                            (count($items) > 1) 
                            && (
                                $isReorderableWithButtons 
                                || $isReorderableWithDragAndDrop
                            )
                        )
                            <th class="fi-fo-table-repeater-empty-header-cell" style="width: 45px"></th>
                        @endif

                        @foreach ($tableColumns as $tableColumn)
                            <th
                                @class([
                                    'fi-wrapped' => $tableColumn->canHeaderWrap(),
                                    (($columnAlignment = $tableColumn->getAlignment()) instanceof Alignment) ? ('fi-align-' . $columnAlignment->value) : $columnAlignment,
                                ])
                                @style([
                                    ('width: ' . ($columnWidth = $tableColumn->getWidth())) => filled($columnWidth),
                                ])
                            >
                                @if (! $tableColumn->isHeaderLabelHidden())
                                    {{ $tableColumn->getLabel() }}

                                    @if ($tableColumn->isMarkedAsRequired())
                                        <sup class="fi-fo-table-repeater-header-required-mark">*</sup>
                                    @endif
                                @else
                                    <span class="fi-sr-only">
                                        {{ $tableColumn->getLabel() }}
                                    </span>
                                @endif
                            </th>
                        @endforeach

                        @if (
                            count($extraItemActions) 
                            || $isCloneable 
                            || $isDeletable
                        )
                            <th class="text-center align-middle fi-fo-table-repeater-empty-header-cell" style="width: 75px">
                                @if ($hasColumnManagerDropdown)
                                    <x-filament::dropdown
                                        shift
                                        placement="bottom-end"
                                        :max-height="$getColumnManagerMaxHeight()"
                                        :width="$getColumnManagerWidth()"
                                        :wire:key="$this->getId() . '.table.column-manager.' . $statePath"
                                        class="inline-block fi-ta-col-manager-dropdown"
                                    >
                                        <x-slot name="trigger">
                                            {{ $columnManagerTriggerAction }}
                                        </x-slot>

                                        <x-support::column-manager
                                            heading-tag="h2"
                                            :apply-action="$columnManagerApplyAction"
                                            :table-columns="$getMappedColumns()"
                                            :columns="$getColumnManagerColumns()"
                                            :has-reorderable-columns="false"
                                            :has-toggleable-columns="$hasToggleableColumns"
                                            :reorder-animation-duration="$getReorderAnimationDuration()"
                                            :repeater-key="$statePath"
                                        />
                                    </x-filament::dropdown>
                                @endif
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody
                    x-sortable
                    {{ 
                        (new ComponentAttributeBag)
                            ->merge([
                                'data-sortable-animation-duration' => $getReorderAnimationDuration(),
                                'x-on:end.stop' => '$event.oldDraggableIndex !== $event.newDraggableIndex && $wire.mountAction(\'reorder\', { items: $event.target.sortable.toArray() }, { schemaComponent: \'' . $key . '\' })',
                            ], escape: false) 
                    }}
                >
                    @foreach ($items as $itemKey => $item)
                        @php
                            $visibleExtraItemActions = collect($extraItemActions)->filter(fn (Action $action) => $action(['item' => $itemKey])->isVisible())->values()->all();
                            $cloneAction = $cloneAction(['item' => $itemKey]);
                            $cloneActionIsVisible = $isCloneable && $cloneAction->isVisible();
                            $deleteAction = $deleteAction(['item' => $itemKey]);
                            $deleteActionIsVisible = $isDeletable && $deleteAction->isVisible();
                            $moveDownAction = $moveDownAction(['item' => $itemKey])->disabled($loop->last);
                            $moveDownActionIsVisible = $isReorderableWithButtons && $moveDownAction->isVisible();
                            $moveUpAction = $moveUpAction(['item' => $itemKey])->disabled($loop->first);
                            $moveUpActionIsVisible = $isReorderableWithButtons && $moveUpAction->isVisible();
                            $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                            $itemStatePath = $item->getStatePath();
                        @endphp

                        <tr
                            wire:key="{{ $item->getLivewireKey() }}.item"
                            x-sortable-item="{{ $itemKey }}"
                        >
                            @if (
                                (count($items) > 1) 
                                && (
                                    $isReorderableWithButtons 
                                    || $isReorderableWithDragAndDrop
                                )
                            )
                                <td class='p-2'>
                                    @if (
                                        $reorderActionIsVisible 
                                        || $moveUpActionIsVisible 
                                        || $moveDownActionIsVisible
                                    )
                                        <div>
                                            @if ($reorderActionIsVisible)
                                                <div x-on:click.stop>
                                                    {{ $reorderAction->extraAttributes(['x-sortable-handle' => true], merge: true) }}
                                                </div>
                                            @endif

                                            @if (
                                                $moveUpActionIsVisible 
                                                || $moveDownActionIsVisible
                                            )
                                                <div x-on:click.stop>
                                                    {{ $moveUpAction }}
                                                </div>

                                                <div x-on:click.stop>
                                                    {{ $moveDownAction }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            @endif

                            @php
                                $counter = 0;
                                $visibleColumns = collect($tableColumns)->mapWithKeys(fn ($tableColumn) => [$tableColumn->getName() => $tableColumn]);
                            @endphp

                            @foreach ($item->getComponents() as $schemaComponent)
                                @php
                                    throw_unless(
                                        $schemaComponent instanceof \Filament\Schemas\Components\Component,
                                        new Exception('Table repeaters must only contain schema components, but [' . $schemaComponent::class . '] was used.'),
                                    );
                                @endphp

                                @if ($visibleColumns->has($schemaComponent->getName()))
                                    @if ($schemaComponent instanceof \Filament\Forms\Components\Hidden)
                                        {{ $schemaComponent }}
                                    @else
                                        <td
                                            @if (! (
                                                $schemaComponent instanceof Action 
                                                || $schemaComponent instanceof ActionGroup
                                            ))
                                                @php
                                                    $schemaComponentStatePath = $schemaComponent->getStatePath();
                                                @endphp

                                                x-data="filamentSchemaComponent({
                                                    path: @js($schemaComponentStatePath),
                                                    containerPath: @js($itemStatePath),
                                                    isLive: @js($schemaComponent->isLive()),
                                                    $wire,
                                                })"
                                            @endif
                                        >
                                            {{ $schemaComponent }}
                                        </td>
                                    @endif
                                @endif
                            @endforeach

                            @if (
                                count($extraItemActions) 
                                || $isCloneable 
                                || $isDeletable
                            )
                                <td>
                                    @if (
                                        $visibleExtraItemActions 
                                        || $cloneActionIsVisible 
                                        || $deleteActionIsVisible
                                    )
                                        <div class="flex flex-row items-center justify-center gap-2">
                                            @foreach ($visibleExtraItemActions as $extraItemAction)
                                                <div x-on:click.stop>
                                                    {{ $extraItemAction(['item' => $itemKey]) }}
                                                </div>
                                            @endforeach

                                            @if ($cloneActionIsVisible)
                                                <div x-on:click.stop>
                                                    {{ $cloneAction }}
                                                </div>
                                            @endif

                                            @if ($deleteActionIsVisible)
                                                <div x-on:click.stop>
                                                    {{ $deleteAction }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="flex items-center justify-center">
        @if (
            $isAddable 
            && $addAction->isVisible()
        )
            <div
                @class([
                    'fi-fo-table-repeater-add',
                    ($addActionAlignment instanceof Alignment) ? ('fi-align-' . $addActionAlignment->value) : $addActionAlignment,
                ])
            >
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
