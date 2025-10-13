@php
    $record = $getRecord();
    $changes = is_array($record->properties) ? $record->properties : [];
@endphp

<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div {{ $attributes->merge($getExtraAttributes())->class('') }}>
        @switch($record->type)
            @case('note')
            @case('comment')
                @if ($record->subject)
                    <div class="mb-3">
                        <span class="block text-xs font-medium tracking-wide text-gray-500 dark:text-gray-400">
                            @lang('chatter::views/filament/infolists/components/messages/content-text-entry.subject')
                        </span>
                        <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $record->subject }}
                        </div>
                    </div>
                @endif

                @if($record->body)
                    <div class="text-sm leading-6 text-gray-700 [overflow-wrap:anywhere] overflow-x-hidden max-w-full [&_a]:[overflow-wrap:anywhere] [&_a]:text-primary-600 dark:[&_a]:text-primary-400 [&_a:hover]:underline [&_ul]:list-disc [&_ul]:ms-5 [&_ol]:list-decimal [&_ol]:ms-5 dark:text-white">
                        {!! $record->body !!}
                    </div>
                @endif

                @if($record->attachments->isNotEmpty())
                    <section class="mt-4">
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($record->attachments->chunk(6) as $chunk)
                                @foreach($chunk as $attachment)
                                    @php
                                        $fileExtension = strtolower(pathinfo($attachment->original_file_name, PATHINFO_EXTENSION));
                                        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);

                                        switch($fileExtension) {
                                            case 'pdf':
                                                $icon = 'heroicon-o-document-text';
                                                break;
                                            case 'sql':
                                                $icon = 'heroicon-o-database';
                                                break;
                                            case 'csv':
                                                $icon = 'heroicon-o-table-cells';
                                                break;
                                            case 'md':
                                                $icon = 'heroicon-o-document';
                                                break;
                                            default:
                                                $icon = 'heroicon-o-document';
                                        }
                                    @endphp

                                    <div class="flex items-center gap-3 p-3 transition rounded-lg group bg-white/70 ring-1 ring-black/5 hover:shadow-sm dark:bg-gray-900/50 dark:ring-white/5">
                                        @if($isImage)
                                            <img
                                                src="{{ Storage::url($attachment->file_path) }}"
                                                alt="{{ $attachment->original_file_name }}"
                                                class="object-cover w-10 h-10 rounded ring-1 ring-black/5 dark:ring-white/10"
                                                loading="lazy"
                                            />
                                        @else
                                            <div class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded ring-1 ring-black/5 dark:bg-gray-800 dark:ring-white/10">
                                                <x-filament::icon :icon="$icon" class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-between flex-1 min-w-0 gap-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-100">{{ $attachment->original_file_name }}</p>
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                @if(
                                                    $isImage
                                                    || in_array($fileExtension, ['pdf'])
                                                )
                                                    <x-filament::button
                                                        size="xs"
                                                        color="gray"
                                                        icon="heroicon-m-eye"
                                                        class="!gap-0"
                                                        icon-only
                                                        tag="a"
                                                        :href="Storage::url($attachment->file_path)"
                                                        target="_blank"
                                                        :tooltip="__('chatter::views/filament/infolists/components/messages/content-text-entry.preview')"
                                                        aria-label="{{ __('chatter::views/filament/infolists/components/messages/content-text-entry.preview') }}"
                                                    />
                                                @endif

                                                <x-filament::button
                                                    size="xs"
                                                    color="gray"
                                                    icon="heroicon-m-arrow-down-tray"
                                                    class="!gap-0"
                                                    icon-only
                                                    tag="a"
                                                    :href="Storage::url($attachment->file_path)"
                                                    download="{{ $attachment->original_file_name }}"
                                                    :tooltip="__('chatter::views/filament/infolists/components/messages/content-text-entry.download')"
                                                    aria-label="{{ __('chatter::views/filament/infolists/components/messages/content-text-entry.download') }}"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </section>
                @endif

                @break
            @case('notification')
                @if ($record->body)
                    <div class="text-sm leading-6 text-gray-900 dark:text-gray-100 [overflow-wrap:anywhere] max-w-full overflow-x-hidden">
                        {!! $record->body !!}
                    </div>
                @endif

                @if (
                    count($changes) > 0
                    && $record->event !== 'created'
                )
                    <div class="mt-3 overflow-hidden shadow-sm rounded-xl bg-white/70 ring-1 ring-black/5 dark:bg-gray-900/60 dark:ring-white/5">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/80 dark:border-gray-800 dark:bg-gray-800/60">
                            <div class="flex items-center gap-2">
                                <x-heroicon-m-arrow-path class="w-5 h-5 text-primary-600 dark:text-primary-400"/>

                                <h3 class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">
                                    @lang('chatter::views/filament/infolists/components/messages/content-text-entry.changes-made')
                                </h3>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($changes as $field => $change)
                                @if(is_array($change))
                                    <div class="p-4">
                                        <div class="flex items-center gap-2 mb-3">
                                            @if($field === 'title')
                                                <x-heroicon-m-pencil-square class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                            @elseif($field === 'due_date')
                                                <x-heroicon-m-calendar class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                            @else
                                                <x-heroicon-m-arrow-path class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                            @endif

                                            <span class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">
                                                @lang('chatter::views/filament/infolists/components/messages/content-text-entry.modified', [
                                                    'field' => ucwords(str_replace('_', ' ', $field)),
                                                ])

                                                @isset($change['type'])
                                                    <span class="inline-flex items-center text-xs rounded-md">
                                                        {{ ucfirst($change['type']) }}
                                                    </span>
                                                @endisset
                                            </span>
                                        </div>

                                        <div class="pl-6 mt-2 space-y-2">
                                            @if(isset($change['old_value']))
                                                <div class="flex items-center gap-2 group">
                                                    <span class="flex-shrink-0">
                                                        <x-heroicon-m-minus-circle
                                                            class="w-4 h-4"
                                                            @style([
                                                                'color: rgb(var(--danger-500))',
                                                            ])
                                                        />
                                                    </span>

                                                    <span
                                                        class="text-sm text-gray-600 transition-colors dark:text-gray-400"
                                                        @style([
                                                            'color: rgb(var(--danger-500))',
                                                        ])
                                                    >
                                                        @if($field === 'due_date')
                                                            {{ \Carbon\Carbon::parse($change['old_value'])->format('F j, Y') }}
                                                        @else
                                                            @if (is_array($change['old_value']))
                                                                {{ implode(', ', $change['old_value']) }}
                                                            @else
                                                                {!! $change['old_value'] !!}
                                                            @endif
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif

                                            @if(isset($change['new_value']))
                                                <div class="flex items-center gap-2 group">
                                                    <span class="flex-shrink-0">
                                                        <x-heroicon-m-plus-circle
                                                            class="w-4 h-4"
                                                            @style([
                                                                'color: rgb(var(--success-500))',
                                                            ])
                                                        />
                                                    </span>

                                                    <span class="text-sm font-medium text-gray-900 transition-colors dark:text-gray-100"
                                                            @style([
                                                                'color: rgb(var(--success-500))',
                                                            ])>
                                                        @if($field === 'due_date')
                                                            {{ \Carbon\Carbon::parse($change['new_value'])->format('F j, Y') }}
                                                        @else
                                                            @if (is_array($change['new_value']))
                                                                {{ implode(', ', $change['new_value']) }}
                                                            @else
                                                                {!! $change['new_value'] !!}
                                                            @endif
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                @break
        @endSwitch
    </div>
</x-dynamic-component>
