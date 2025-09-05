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
                            'gap-2',
                        ])
                }}
            >
                @php $lastDateLabel = null; @endphp
                @foreach ($childComponentContainers as $container)
                    @php
                        $createdAt = data_get($container->getRecord(), 'created_at');

                        try {
                            $dt = $createdAt instanceof \Carbon\CarbonInterface ? $createdAt : \Carbon\Carbon::parse($createdAt);
                        } catch (\Throwable $e) {
                            $dt = null;
                        }

                        $currentLabel = '';

                        if ($dt) {
                            if ($dt->isToday()) {
                                $currentLabel = __('chatter::views/filament/infolists/components/messages/repeatable-entry.today');
                            } elseif ($dt->isYesterday()) {
                                $currentLabel = __('chatter::views/filament/infolists/components/messages/repeatable-entry.yesterday');
                            } else {
                                $currentLabel = $dt->format('M j, Y');
                            }
                        }
                    @endphp

                    @if ($currentLabel && $currentLabel !== $lastDateLabel)
                        <div class="relative mb-4" role="separator" aria-label="{{ $currentLabel }}">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent dark:via-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="inline-flex items-center bg-white/95 px-2 py-0 text-[11px] font-medium uppercase tracking-wide text-gray-600 dark:bg-gray-950/80 dark:text-gray-300">
                                    {{ $currentLabel }}
                                </span>
                            </div>
                        </div>
                        @php $lastDateLabel = $currentLabel; @endphp
                    @endif
            <article
                        @class([
                            'mb-4 rounded-xl p-4 text-base shadow-sm ring-1 transition-shadow hover:shadow-md overflow-x-hidden [overflow-wrap:anywhere]',
                            'bg-gray-50 ring-gray-200 dark:bg-gray-800/50 dark:ring-gray-800' => data_get($container->getRecord(), 'type') === 'note',
                            'bg-white/70 ring-black/5 dark:bg-gray-900/60 dark:ring-white/5' => data_get($container->getRecord(), 'type') !== 'note',
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
