@php
    $trans = 'support::filament/clusters/manage-branding.form.preview';

    $g = fn (int $s): string => $palettes['gray_color'][$s];
    $pc = fn (string $f, int $s): string => $palettes[$f][$s];
    $mix = fn (string $color, int $pct): string => 'color-mix(in oklab, '.$color.' '.$pct.'%, transparent)';

    $statuses = [
        'success' => 'success_color',
        'info'    => 'info_color',
        'warning' => 'warning_color',
        'danger'  => 'danger_color',
    ];

    $themes = [
        'light' => [
            'page'        => $g(50),
            'panel'       => '#ffffff',
            'border'      => $g(200),
            'text'        => $g(900),
            'muted'       => $g(500),
            'activeBg'    => $pc('primary_color', 50),
            'activeText'  => $pc('primary_color', 700),
            'primaryBg'   => $button['bg'],
            'primaryText' => $button['text'],
            'secBorder'   => $g(300),
            'secText'     => $g(700),
            'inputBg'     => '#ffffff',
            'inputBorder' => $g(300),
            'alertBg'     => $pc('info_color', 50),
            'alertText'   => $pc('info_color', 700),
            'alertBorder' => $pc('info_color', 200),
            'link'        => $pc('primary_color', 600),
            'badges'      => [],
        ],
        'dark' => [
            'page'        => $g(950),
            'panel'       => $g(900),
            'border'      => $g(800),
            'text'        => $g(50),
            'muted'       => $g(400),
            'activeBg'    => $mix($pc('primary_color', 400), 15),
            'activeText'  => $pc('primary_color', 400),
            'primaryBg'   => $button['darkBg'],
            'primaryText' => $button['darkText'],
            'secBorder'   => $g(700),
            'secText'     => $g(200),
            'inputBg'     => $g(950),
            'inputBorder' => $g(700),
            'alertBg'     => $mix($pc('info_color', 400), 12),
            'alertText'   => $pc('info_color', 300),
            'alertBorder' => $mix($pc('info_color', 400), 25),
            'link'        => $pc('primary_color', 400),
            'badges'      => [],
        ],
    ];

    foreach ($statuses as $name => $field) {
        $themes['light']['badges'][$name] = ['bg' => $pc($field, 50), 'text' => $pc($field, 700)];
        $themes['dark']['badges'][$name] = ['bg' => $mix($pc($field, 400), 15), 'text' => $pc($field, 400)];
    }

    $navItems = [
        ['icon' => 'heroicon-o-home', 'key' => 'dashboard', 'active' => true],
        ['icon' => 'heroicon-o-shopping-bag', 'key' => 'products', 'active' => false],
        ['icon' => 'heroicon-o-clipboard-document-list', 'key' => 'orders', 'active' => false],
        ['icon' => 'heroicon-o-users', 'key' => 'customers', 'active' => false],
        ['icon' => 'heroicon-o-cog-6-tooth', 'key' => 'settings', 'active' => false],
    ];
@endphp

<div class="flex flex-col gap-5" x-data="{ theme: 'light' }">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="h-10 w-10 rounded-full ring-1 ring-gray-950/10 dark:ring-white/10" style="background-color: {{ $focus['hex'] }}"></span>

            <div class="flex flex-col">
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $focus['label'] }}</span>

                <span class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ strtoupper($focus['hex']) }}</span>
            </div>
        </div>

        @if ($focus['key'] === 'primary_color')
            <span class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-600 dark:border-white/10 dark:text-gray-300">
                {{ __($trans.'.shade-note') }}

                <x-filament::icon icon="heroicon-m-chevron-down" class="h-4 w-4 text-gray-400" />
            </span>
        @endif
    </div>

    <div class="flex items-end gap-1.5">
        @foreach ($focus['shades'] as $shade => $color)
            <div class="flex min-w-0 flex-1 flex-col items-center gap-1">
                <span @class([
                    'w-full rounded-lg',
                    'h-9 ring-1 ring-gray-950/5 dark:ring-white/10' => $shade !== 600,
                    'h-11 ring-2 ring-primary-500 ring-offset-1 ring-offset-white dark:ring-offset-gray-900' => $shade === 600,
                ]) style="background-color: {{ $color }}" title="{{ $shade }}"></span>

                <span @class([
                    'text-[10px] tabular-nums',
                    'font-bold text-gray-900 dark:text-white' => $shade === 600,
                    'text-gray-500 dark:text-gray-400' => $shade !== 600,
                ])>{{ $shade }}</span>
            </div>
        @endforeach
    </div>

    <div class="inline-flex gap-1 self-start rounded-lg bg-gray-100 p-1 dark:bg-white/5" role="tablist">
        <button type="button" role="tab" @click="theme = 'light'" :aria-selected="theme === 'light'"
            :class="theme === 'light' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
            class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition">
            <x-filament::icon icon="heroicon-o-sun" class="h-4 w-4" />{{ __($trans.'.light') }}
        </button>

        <button type="button" role="tab" @click="theme = 'dark'" :aria-selected="theme === 'dark'"
            :class="theme === 'dark' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
            class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition">
            <x-filament::icon icon="heroicon-o-moon" class="h-4 w-4" />{{ __($trans.'.dark') }}
        </button>
    </div>

    @foreach ($themes as $mode => $t)
        <div @if ($mode === 'dark') x-cloak @endif x-show="theme === '{{ $mode }}'" class="overflow-hidden rounded-xl border" style="border-color: {{ $t['border'] }}; background-color: {{ $t['page'] }};">
            <div class="flex items-center gap-2 border-b px-3 py-2" style="border-color: {{ $t['border'] }}; background-color: {{ $t['panel'] }};">
                <span class="text-sm font-bold" style="color: {{ $t['text'] }}">{{ config('app.name') }}</span>

                <div class="ms-2 flex h-7 min-w-0 flex-1 items-center rounded-lg border px-2 text-xs" style="border-color: {{ $t['inputBorder'] }}; color: {{ $t['muted'] }};">{{ __($trans.'.search') }}</div>

                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg" style="color: {{ $t['secText'] }};">
                    <x-filament::icon icon="heroicon-o-bell" class="h-4 w-4" />
                </span>
            </div>

            <div class="flex">
                <div class="flex w-32 shrink-0 flex-col gap-1 border-e p-2 sm:w-36" style="border-color: {{ $t['border'] }}; background-color: {{ $t['panel'] }};">
                    @foreach ($navItems as $item)
                        <div class="flex min-w-0 items-center gap-2 rounded-lg px-2 py-1.5 text-xs font-medium"
                            @style([
                                "background-color: {$t['activeBg']}; color: {$t['activeText']};" => $item['active'],
                                "color: {$t['muted']};" => ! $item['active'],
                            ])>
                            <x-filament::icon :icon="$item['icon']" class="h-4 w-4 shrink-0" />

                            <span class="truncate">{{ __($trans.'.nav.'.$item['key']) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex min-w-0 flex-1 flex-col gap-3 p-3">
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-lg px-3 py-1.5 text-xs font-semibold" style="background-color: {{ $t['primaryBg'] }}; color: {{ $t['primaryText'] }};">{{ __($trans.'.primary-button') }}</span>

                        <span class="rounded-lg border px-3 py-1.5 text-xs font-semibold" style="border-color: {{ $t['secBorder'] }}; color: {{ $t['secText'] }};">{{ __($trans.'.secondary-button') }}</span>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($t['badges'] as $name => $badge)
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-medium" style="background-color: {{ $badge['bg'] }}; color: {{ $badge['text'] }};">{{ __($trans.'.status.'.$name) }}</span>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2 rounded-lg border px-3 py-2 text-xs" style="background-color: {{ $t['alertBg'] }}; border-color: {{ $t['alertBorder'] }}; color: {{ $t['alertText'] }};">
                        <x-filament::icon icon="heroicon-o-information-circle" class="h-4 w-4 shrink-0" />

                        <span>{{ __($trans.'.alert') }}</span>
                    </div>

                    <div class="rounded-lg border p-3" style="border-color: {{ $t['border'] }}; background-color: {{ $t['panel'] }};">
                        <h4 class="text-sm font-semibold" style="color: {{ $t['text'] }}">{{ __($trans.'.card-title') }}</h4>

                        <p class="mt-1 text-xs" style="color: {{ $t['muted'] }}">{{ __($trans.'.card-body') }}</p>

                        <a href="#" @click.prevent class="mt-1 inline-block text-xs font-medium underline underline-offset-2 cursor-default" style="color: {{ $t['link'] }}">{{ __($trans.'.link') }}</a>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold" style="color: {{ $t['text'] }}">{{ __($trans.'.input-label') }}</span>

                        <div class="flex h-8 items-center rounded-lg border px-2 text-xs" style="background-color: {{ $t['inputBg'] }}; border-color: {{ $t['inputBorder'] }}; color: {{ $t['muted'] }};">{{ __($trans.'.input-placeholder') }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
