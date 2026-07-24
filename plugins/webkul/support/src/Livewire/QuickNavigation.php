<?php

namespace Webkul\Support\Livewire;

use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Throwable;
use Webkul\Support\Services\QuickNavigationFavorites;
use Webkul\Support\Services\QuickNavigator;
use Webkul\Support\Services\QuickNavigationRecents;

class QuickNavigation extends Component
{
    public string $query = '';

    public ?string $parent = null;

    public ?string $parentLabel = null;

    protected const NAVIGATION_LIMIT = 50;

    protected ?array $urlMeta = null;

    public function boot(): void
    {
        abort_unless(Filament::auth()->check(), 403);
    }

    protected function navigator(): QuickNavigator
    {
        return new QuickNavigator;
    }

    /**
     * Map of url => ['icon', 'context'], giving favourites/recents their real icon and location.
     */
    protected function urlMeta(): array
    {
        if ($this->urlMeta !== null) {
            return $this->urlMeta;
        }

        $map = [];

        foreach ($this->navigator()->flat() as $entry) {
            $node = $entry['node'];

            if (empty($node['url'])) {
                continue;
            }

            $map[$node['url']] ??= [
                'icon'    => $node['icon'] ?? null,
                'context' => $entry['group'].($entry['section'] ? ' / '.$entry['section'] : ''),
            ];
        }

        foreach ($this->navigator()->createNodes() as $node) {
            if (empty($node['url'])) {
                continue;
            }

            $map[$node['url']] ??= [
                'icon'    => $node['icon'] ?? null,
                'context' => $map[$node['indexUrl'] ?? '']['context'] ?? __('support::quick-navigation.create'),
            ];
        }

        return $this->urlMeta = $map;
    }

    protected function iconForUrl(string $url, string $fallback): string
    {
        $icon = $this->urlMeta()[$url]['icon'] ?? null;

        return $this->iconHtml($icon) ?? $this->iconHtml($fallback);
    }

    protected function contextForUrl(string $url): ?string
    {
        return $this->urlMeta()[$url]['context'] ?? null;
    }

    #[Computed]
    public function groups(): array
    {
        $query = trim($this->query);

        if ($query !== '' && $this->parent !== null) {
            return $this->decorate($this->scopedSearchGroups($query));
        }

        if ($query !== '') {
            return $this->decorate(array_values(array_filter(array_merge(
                $this->searchGroups($query),
                $this->createGroups($query),
                $this->systemGroups($query),
                $this->recordGroups($query),
            ))));
        }

        if ($this->parent !== null) {
            return $this->decorate($this->childGroups());
        }

        return $this->decorate(array_values(array_filter(array_merge(
            $this->favoriteGroups(),
            $this->recentGroups(),
            $this->defaultGroups(),
            $this->systemGroups(''),
        ))));
    }

    public function toggleFavorite(string $url, string $label): void
    {
        QuickNavigationFavorites::toggle($url, $label);

        unset($this->groups);
    }

    protected function favoriteGroups(): array
    {
        $items = [];

        foreach (QuickNavigationFavorites::get() as $favorite) {
            $items[] = [
                'id'       => 'favorite.'.md5($favorite['url']),
                'title'    => $favorite['label'],
                'subtitle' => $this->contextForUrl($favorite['url']),
                'icon'     => $this->iconForUrl($favorite['url'], 'heroicon-o-chevron-right'),
                'url'      => $favorite['url'],
            ];
        }

        if ($items === []) {
            return [];
        }

        return [[
            'label' => __('support::quick-navigation.favorites'),
            'items' => $items,
        ]];
    }

    protected function createGroups(string $query): array
    {
        $items = [];

        foreach ($this->navigator()->createNodes() as $node) {
            $context = $this->contextForUrl($node['url']);

            if ($query !== '' && $this->rank($query, $node['label'], trim(($node['keywords'] ?? '').' '.$context)) === null) {
                continue;
            }

            $item = $this->presentNode($node);
            $item['subtitle'] = $context;

            $items[] = $item;
        }

        if ($items === []) {
            return [];
        }

        usort($items, fn (array $a, array $b): int => [$a['subtitle'], $a['title']] <=> [$b['subtitle'], $b['title']]);

        return [[
            'label' => __('support::quick-navigation.create'),
            'items' => $items,
        ]];
    }

    /**
     * Flag every url-bearing item as favouritable / favourited.
     */
    protected function decorate(array $groups): array
    {
        $favorites = QuickNavigationFavorites::urls();

        foreach ($groups as &$group) {
            foreach ($group['items'] as &$item) {
                if (empty($item['url'])) {
                    continue;
                }

                $item['canFavorite'] = true;
                $item['favorite'] = in_array($item['url'], $favorites, true);
            }
        }

        return $groups;
    }

    public function drill(string $clusterId, string $label, ?string $group = null): void
    {
        $this->parent = $clusterId;
        $this->parentLabel = $group ? $group.' / '.$label : $label;
        $this->reset('query');
        $this->dispatch('quick-navigation-reset');
    }

    public function back(): void
    {
        $this->reset('query', 'parent', 'parentLabel');
        $this->dispatch('quick-navigation-reset');
    }

    public function resetState(): void
    {
        $this->reset('query', 'parent', 'parentLabel');
    }

    public function goto(string $url, ?string $title = null): void
    {
        if ($title !== null) {
            QuickNavigationRecents::push($url, $title);
        }

        $this->reset('query', 'parent', 'parentLabel');

        $this->redirect($url, navigate: true);
    }

    protected function defaultGroups(): array
    {
        return array_map(
            fn (array $group): array => [
                'label' => $group['label'],
                'items' => array_map(fn (array $node): array => $this->presentNode($node), $group['nodes']),
            ],
            $this->navigator()->groups(),
        );
    }

    protected function childGroups(): array
    {
        if ($this->parent === null) {
            return [];
        }

        $children = $this->navigator()->clusterChildren($this->parent);

        if ($children === null || $children['nodes'] === []) {
            return [];
        }

        return [[
            'label' => $children['label'],
            'items' => array_map(fn (array $node): array => $this->presentNode($node), $children['nodes']),
        ]];
    }

    protected function scopedSearchGroups(string $query): array
    {
        if ($this->parent === null) {
            return [];
        }

        $children = $this->navigator()->clusterChildren($this->parent);

        $navItems = [];

        foreach ($children['nodes'] ?? [] as $node) {
            if ($this->rank($query, $node['label'], $children['label'] ?? '') === null) {
                continue;
            }

            $navItems[] = $this->presentNode($node);
        }

        $groups = [];

        if ($navItems !== []) {
            $groups[] = ['label' => $children['label'], 'items' => $navItems];
        }

        return array_values(array_filter(array_merge(
            $groups,
            $this->scopedRecordGroups($query),
        )));
    }

    protected function scopedRecordGroups(string $query): array
    {
        $groups = [];

        foreach ($this->navigator()->clusterResources($this->parent) as $resource) {
            try {
                if (! $resource::canGloballySearch()) {
                    continue;
                }

                $results = $resource::getGlobalSearchResults($query);
            } catch (Throwable) {
                continue;
            }

            if (! $results || ! $results->count()) {
                continue;
            }

            $label = Str::ucfirst($resource::getPluralModelLabel());
            $items = [];

            foreach ($results as $result) {
                $items[] = [
                    'id'       => 'record.'.md5($resource.'|'.$result->url),
                    'title'    => (string) $result->title,
                    'subtitle' => $this->recordSubtitle($label, $result->details ?? []),
                    'icon'     => $this->iconHtml('heroicon-o-arrow-right-circle'),
                    'url'      => $result->url,
                ];
            }

            if ($items !== []) {
                $groups[] = ['label' => $label, 'items' => $items];
            }
        }

        return $groups;
    }

    protected function searchGroups(string $query): array
    {
        $matches = [];
        $seen = [];

        foreach ($this->navigator()->flat() as $entry) {
            $node = $entry['node'];
            $url = $node['url'] ?? null;

            if ($url === null || isset($seen[$url])) {
                continue;
            }

            $context = trim($entry['group'].' '.($entry['section'] ?? ''));
            $score = $this->rank($query, $node['label'], $context);

            if ($score === null) {
                continue;
            }

            $seen[$url] = true;
            $matches[] = ['entry' => $entry, 'node' => $node, 'score' => $score];
        }

        usort($matches, fn (array $a, array $b): int => $b['score'] <=> $a['score']);

        $groups = [];

        foreach (array_slice($matches, 0, static::NAVIGATION_LIMIT) as $match) {
            $group = $match['entry']['group'];

            $item = $this->presentNode($match['node']);
            $item['subtitle'] = $match['entry']['section'];

            $groups[$group] ??= ['label' => $group, 'items' => []];
            $groups[$group]['items'][] = $item;
        }

        return array_values($groups);
    }

    protected function recordGroups(string $query): array
    {
        $provider = Filament::getCurrentPanel()?->getGlobalSearchProvider();

        if ($provider === null) {
            return [];
        }

        try {
            $results = $provider->getResults($query);
        } catch (Throwable) {
            return [];
        }

        if ($results === null) {
            return [];
        }

        $groups = [];

        foreach ($results->getCategories() as $category => $entries) {
            $plugin = Str::of((string) $category)->before('→')->squish()->toString();
            $label = Str::of((string) $category)->afterLast('→')->squish()->ucfirst()->toString();

            foreach ($entries as $entry) {
                $groups[$plugin] ??= ['label' => $plugin, 'items' => []];
                $groups[$plugin]['items'][] = [
                    'id'       => 'record.'.md5($category.'|'.$entry->url),
                    'title'    => (string) $entry->title,
                    'subtitle' => $this->recordSubtitle($label, $entry->details ?? []),
                    'icon'     => $this->iconHtml('heroicon-o-arrow-right-circle'),
                    'url'      => $entry->url,
                ];
            }
        }

        return array_values($groups);
    }

    protected function recentGroups(): array
    {
        $items = [];

        foreach (QuickNavigationRecents::get() as $recent) {
            $items[] = [
                'id'       => 'recent.'.md5($recent['url']),
                'title'    => $recent['title'],
                'subtitle' => $this->contextForUrl($recent['url']),
                'icon'     => $this->iconForUrl($recent['url'], 'heroicon-o-chevron-right'),
                'url'      => $recent['url'],
            ];
        }

        if ($items === []) {
            return [];
        }

        return [[
            'label' => __('support::quick-navigation.recent'),
            'items' => $items,
        ]];
    }

    protected function systemGroups(string $query): array
    {
        $query = trim($query);

        $items = array_values(array_filter(
            $this->systemItems(),
            fn (array $item): bool => $query === '' || $this->rank($query, $item['title'], '') !== null,
        ));

        if ($items === []) {
            return [];
        }

        return [[
            'label' => __('support::quick-navigation.system'),
            'items' => $items,
        ]];
    }

    protected function systemItems(): array
    {
        return [
            [
                'id'       => 'system.copy-url',
                'title'    => __('support::quick-navigation.copy_url'),
                'subtitle' => __('support::quick-navigation.system'),
                'icon'     => $this->iconHtml('heroicon-o-clipboard-document'),
                'action'   => 'copy-url',
            ],
            [
                'id'       => 'system.new-tab',
                'title'    => __('support::quick-navigation.new_tab'),
                'subtitle' => __('support::quick-navigation.system'),
                'icon'     => $this->iconHtml('heroicon-o-arrow-top-right-on-square'),
                'action'   => 'open-new-tab',
            ],
            [
                'id'       => 'system.theme',
                'title'    => __('support::quick-navigation.toggle_theme'),
                'subtitle' => __('support::quick-navigation.system'),
                'icon'     => $this->iconHtml('heroicon-o-moon'),
                'action'   => 'toggle-theme',
            ],
            [
                'id'       => 'system.logout',
                'title'    => __('support::quick-navigation.logout'),
                'subtitle' => __('support::quick-navigation.system'),
                'icon'     => $this->iconHtml('heroicon-o-power'),
                'action'   => 'logout',
            ],
        ];
    }

    protected function presentNode(array $node): array
    {
        $item = [
            'id'       => $node['id'],
            'title'    => $node['label'],
            'subtitle' => null,
            'icon'     => $this->iconHtml($node['icon'] ?? null),
        ];

        if (! empty($node['clusterId'])) {
            $item['cluster'] = $node['clusterId'];
        } else {
            $item['url'] = $node['url'];
        }

        return $item;
    }

    protected function iconHtml(mixed $icon): ?string
    {
        if ($icon === null) {
            return null;
        }

        try {
            return \Filament\Support\generate_icon_html($icon)?->toHtml();
        } catch (Throwable) {
            return null;
        }
    }

    protected function recordSubtitle(string $label, array $details): string
    {
        $detail = collect($details)->filter()->first();

        return Str::of($label)
            ->when($detail, fn (Stringable $string) => $string->append(' · '.$detail))
            ->toString();
    }

    protected function rank(string $query, string $label, string $context): ?int
    {
        $query = Str::lower(trim($query));

        if ($query === '') {
            return 0;
        }

        $label = Str::lower($label);
        $context = Str::lower($context);
        $haystack = trim($label.' '.$context);

        foreach (preg_split('/\s+/', $query) as $token) {
            if ($token !== '' && ! str_contains($haystack, $token)) {
                return null;
            }
        }

        if (str_starts_with($label, $query)) {
            return 100;
        }

        if (str_contains($label, $query)) {
            return 80;
        }

        if ($this->containsAllTokens($label, $query)) {
            return 60;
        }

        if (str_contains($context, $query)) {
            return 40;
        }

        return 20;
    }

    protected function containsAllTokens(string $haystack, string $query): bool
    {
        foreach (preg_split('/\s+/', $query) as $token) {
            if ($token !== '' && ! str_contains($haystack, $token)) {
                return false;
            }
        }

        return true;
    }

    public function render(): View
    {
        return view('support::quick-navigation');
    }
}
