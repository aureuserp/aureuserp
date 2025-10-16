<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
        }}
    >
        @if (count($childComponentContainers = $getChildComponentContainers()))
            <div
                {{
                    \Filament\Support\prepare_inherited_attributes($attributes)
                        ->merge($getExtraAttributes(), escape: false)
                        ->class([
                            'flex flex-col gap-4',
                        ])
                }}
            >
                @php $lastDateLabel = null; @endphp

                @foreach ($childComponentContainers as $container)
                   <article
                        @class([
                            'rounded-xl p-4 text-base space-y-1',
                            'bg-gray-50 dark:bg-gray-800/50' => data_get($container->getRecord(), 'type') === 'note',
                            'bg-white/70 dark:bg-gray-900/60' => data_get($container->getRecord(), 'type') !== 'note',
                        ])
                    >
                        {{ $container }}
                    </article>
                @endforeach
            </div>
        @elseif (($placeholder = $getPlaceholder()) !== null)
            <div class="text-sm leading-6 text-gray-400 fi-in-placeholder dark:text-gray-500">
                {{ $placeholder }}
            </div>
        @endif
    </div>
</x-dynamic-component>
