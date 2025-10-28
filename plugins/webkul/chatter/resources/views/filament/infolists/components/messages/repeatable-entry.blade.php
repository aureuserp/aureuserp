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
        @if (count($childComponentContainers = $getChildComponentContainers()))
            <div
                {{
                    \Filament\Support\prepare_inherited_attributes($attributes)
                        ->merge($getExtraAttributes(), escape: false)
                        ->class(['flex flex-col gap-3'])
                }}
            >
                @php $lastDateLabel = null; @endphp

                @foreach ($childComponentContainers as $container)
                    @php
                        $createdAt = data_get($container->getRecord(), 'created_at');
                        $dt = null;
                        try {
                            $dt = $createdAt instanceof \Carbon\CarbonInterface
                                ? $createdAt
                                : \Carbon\Carbon::parse($createdAt);
                        } catch (\Throwable $e) {}
                        $currentLabel = match (true) {
                            $dt?->isToday() => __('chatter::views/filament/infolists/components/messages/repeatable-entry.today'),
                            $dt?->isYesterday() => __('chatter::views/filament/infolists/components/messages/repeatable-entry.yesterday'),
                            $dt => $dt->format('M j, Y'),
                            default => null,
                        };
                    @endphp

                    @if ($currentLabel && $currentLabel !== $lastDateLabel)
                        <div class="relative" role="separator" aria-label="{{ $currentLabel }}">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent dark:via-gray-700"></div>
                            </div>

                            <div class="relative flex justify-center">
                                <span class="inline-flex items-center px-2 py-0 text-[11px] font-medium uppercase tracking-wide text-gray-600 rounded-md bg-white/95 dark:bg-gray-950/80 dark:text-gray-300">
                                    {{ $currentLabel }}
                                </span>
                            </div>
                        </div>

                        @php $lastDateLabel = $currentLabel; @endphp
                    @endif

                    <article
                        @class([
                            'rounded-xl px-3 py-2 text-base overflow-x-hidden [overflow-wrap:anywhere] space-y-1 m-0.5',
                            'bg-gray-50/80 ring-gray-200 dark:bg-gray-950 dark:ring-gray-800' => data_get($container->getRecord(), 'type') === 'note',
                            'ring-black/5  dark:ring-white/5' => data_get($container->getRecord(), 'type') !== 'note',
                            'shadow-sm ring-1 bg-gray-50/80 dark:bg-gray-950' => ! (
                                data_get($container->getRecord(), 'type') === 'notification' &&
                                data_get($container->getRecord(), 'event') === 'updated'
                            ),
                        ])
                    >
                        {{ $container }}
                    </article>

                @endforeach
            </div>
        @elseif ($placeholder = $getPlaceholder())
            <div class="text-sm leading-6 text-gray-400 fi-in-placeholder dark:text-gray-500">
                {{ $placeholder }}
            </div>
        @endif
    </div>
</x-dynamic-component>