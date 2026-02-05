<div>
    @php
        $navigation = filament()->getNavigation();
        $isRtl = __('filament-panels::layout.direction') === 'rtl';
        $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();
        $isSidebarFullyCollapsibleOnDesktop = filament()->isSidebarFullyCollapsibleOnDesktop();
        $isAdminPanel = filament()->getCurrentPanel()->getId() === 'admin';
    @endphp

    {{-- format-ignore-start --}}
    <aside
        x-data="{}"
        @if ($isSidebarCollapsibleOnDesktop || $isSidebarFullyCollapsibleOnDesktop)
            x-cloak
        @else
            x-cloak="-lg"
        @endif
        x-bind:class="{ 'fi-sidebar-open': $store.sidebar.isOpen }"
        class="fi-sidebar fi-main-sidebar"
    >
        <div class="fi-sidebar-header-ctn">
            <header
                class="fi-sidebar-header"
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_LOGO_BEFORE) }}

	            @if ($homeUrl = filament()->getHomeUrl())
                    <a {{ \Filament\Support\generate_href_html($homeUrl) }}>
                        <x-filament-panels::logo />
                    </a>
                @else
                    <x-filament-panels::logo />
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_LOGO_AFTER) }}
            </header>
        </div>

        <nav class="fi-sidebar-nav">
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_NAV_START) }}

            @if (filament()->hasTenancy() && filament()->hasTenantMenu())
                <div
                    class="fi-sidebar-nav-tenant-menu-ctn"
                >
                    <x-filament-panels::tenant-menu />
                </div>
            @endif

            <ul class="fi-sidebar-nav-groups">
                @foreach ($navigation as $group)
                    @php
                        $isGroupActive = $group->isActive();
                        $isGroupCollapsible = $group->isCollapsible();
                        $groupIcon = $group->getIcon();
                        $groupItems = $group->getItems();
                        $groupLabel = $group->getLabel();
                        $groupExtraSidebarAttributeBag = $group->getExtraSidebarAttributeBag();

                        if ($isAdminPanel && ! $isGroupActive) {
                            continue;
                        }
                    @endphp

                    {{-- <x-filament-panels::sidebar.group
                        :active="$isGroupActive"
                        :collapsible="$isGroupCollapsible"
                        :icon="$groupIcon"
                        :items="$groupItems"
                        :label="$groupLabel"
                        :attributes="\Filament\Support\prepare_inherited_attributes($groupExtraSidebarAttributeBag)"
                    /> --}}
                    <ul class="fi-sidebar-group-items">
                        @foreach ($groupItems as $item)
                            @php
                                $isItemActive = $item->isActive();
                                $isItemChildItemsActive = $item->isChildItemsActive();
                                $itemActiveIcon = $item->getActiveIcon();
                                $itemBadge = $item->getBadge();
                                $itemBadgeColor = $item->getBadgeColor();
                                $itemBadgeTooltip = $item->getBadgeTooltip();
                                $itemChildItems = $item->getChildItems();
                                $itemIcon = $item->getIcon();
                                $shouldItemOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                $itemUrl = $item->getUrl();
                            @endphp

                            <x-filament-panels::sidebar.item
                                :active="$isItemActive"
                                :active-child-items="$isItemChildItemsActive"
                                :active-icon="$itemActiveIcon"
                                :badge="$itemBadge"
                                :badge-color="$itemBadgeColor"
                                :badge-tooltip="$itemBadgeTooltip"
                                :child-items="$itemChildItems"
                                :first="$loop->first"
                                :grouped="false"
                                :icon="$itemIcon"
                                :last="$loop->last"
                                :should-open-url-in-new-tab="$shouldItemOpenUrlInNewTab"
                                sidebar-collapsible="true"
                                :url="$itemUrl"
                            >
                                {{ $item->getLabel() }}

                                @if ($itemIcon instanceof \Illuminate\Contracts\Support\Htmlable)
                                    <x-slot name="icon">
                                        {{ $itemIcon }}
                                    </x-slot>
                                @endif

                                @if ($itemActiveIcon instanceof \Illuminate\Contracts\Support\Htmlable)
                                    <x-slot name="activeIcon">
                                        {{ $itemActiveIcon }}
                                    </x-slot>
                                @endif
                            </x-filament-panels::sidebar.item>
                        @endforeach
                    </ul>
                @endforeach
            </ul>

            <script>
                var collapsedGroups = JSON.parse(
                    localStorage.getItem('collapsedGroups'),
                )

                if (collapsedGroups === null || collapsedGroups === 'null') {
                    localStorage.setItem(
                        'collapsedGroups',
                        JSON.stringify(@js(
                        collect($navigation)
                            ->filter(fn (\Filament\Navigation\NavigationGroup $group): bool => $group->isCollapsed())
                            ->map(fn (\Filament\Navigation\NavigationGroup $group): string => $group->getLabel())
                            ->values()
                            ->all()
                    )),
                    )
                }

                collapsedGroups = JSON.parse(
                    localStorage.getItem('collapsedGroups'),
                )

                document
                    .querySelectorAll('.fi-sidebar-group')
                    .forEach((group) => {
                        if (
                            !collapsedGroups.includes(group.dataset.groupLabel)
                        ) {
                            return
                        }

                        // Alpine.js loads too slow, so attempt to hide a
                        // collapsed sidebar group earlier.
                        group.querySelector(
                            '.fi-sidebar-group-items',
                        ).style.display = 'none'
                        group.classList.add('fi-collapsed')
                    })
            </script>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_NAV_END) }}
        </nav>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_FOOTER) }}
    </aside>
    {{-- format-ignore-end --}}

    <x-filament-actions::modals />
</div>
