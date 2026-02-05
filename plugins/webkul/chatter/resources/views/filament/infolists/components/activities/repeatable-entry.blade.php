<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div
        {{
            $attributes
                ->merge(['id' => $getId()], escape: false)
                ->merge($getExtraAttributes(), escape: false)
        }}
    >
        @php
            $childComponentContainers = $getChildComponentContainers();
        @endphp

        @if (count($childComponentContainers))
            <div
                {{
                    \Filament\Support\prepare_inherited_attributes($attributes)
                        ->merge($getExtraAttributes(), escape: false)
                        ->class('flex flex-col gap-4')
                }}
            >
                @foreach ($childComponentContainers as $container)
                    @php
                        $recordType = data_get($container->getRecord(), 'type');
                        $isNote = $recordType === 'note';
                    @endphp

                    <article
                        @class([
                            'container rounded-xl px-4 text-base space-y-1',
                            'bg-gray-50 dark:bg-gray-800/50' => $isNote,
                            'bg-white/70 dark:bg-gray-900/60' => ! $isNote,
                        ])
                    >
                        {{ $container }}
                    </article>
                @endforeach
            </div>
        @elseif ($placeholder = $getPlaceholder())
            <div class="text-sm leading-6 text-gray-400 dark:text-gray-500">
                {{ $placeholder }}
            </div>
        @endif
    </div>
</x-dynamic-component>