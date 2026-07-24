<?php

namespace Webkul\Support\Services;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Support\Str;
use Throwable;

class QuickNavigator
{
    /**
     * @var array<string, array{class: string, id: string, label: string}>|null
     */
    protected ?array $clusters = null;

    /**
     * Top-level navigation grouped exactly like the sidebar.
     *
     * @return array<int, array{label: string, nodes: array<int, array>}>
     */
    public function groups(): array
    {
        $panel = Filament::getCurrentPanel();

        if ($panel === null) {
            return [];
        }

        $clusters = $this->clusterMap();
        $groups = [];

        foreach ($panel->getNavigation() as $group) {
            $nodes = [];

            foreach ($group->getItems() as $item) {
                $url = $item->getUrl();
                $cluster = $url !== null ? ($clusters[$this->path($url)] ?? null) : null;

                $nodes[] = [
                    'id'        => $cluster['id'] ?? 'nav.'.md5(($group->getLabel() ?? '').'|'.$item->getLabel().'|'.$url),
                    'label'     => $item->getLabel(),
                    'icon'      => $item->getIcon(),
                    'url'       => $cluster ? null : $url,
                    'clusterId' => $cluster['id'] ?? null,
                ];
            }

            if ($nodes === []) {
                continue;
            }

            $groups[] = [
                'label' => $group->getLabel() ?? __('support::quick-navigation.general'),
                'nodes' => $nodes,
            ];
        }

        return $groups;
    }

    /**
     * Children of a cluster (its sub-menus).
     *
     * @return array{label: string, nodes: array<int, array>}|null
     */
    public function clusterChildren(string $clusterId): ?array
    {
        $cluster = $this->clusterClass($clusterId);

        if ($cluster === null) {
            return null;
        }

        $nodes = [];

        foreach ($cluster::getClusteredComponents() as $component) {
            $node = $this->componentNode($component);

            if ($node !== null) {
                $nodes[] = $node;
            }
        }

        usort($nodes, fn (array $a, array $b): int => ($a['sort'] ?? PHP_INT_MAX) <=> ($b['sort'] ?? PHP_INT_MAX));

        return [
            'label' => $cluster::getNavigationLabel(),
            'nodes' => $nodes,
        ];
    }

    /**
     * "Create" nodes for every resource the user can create.
     *
     * @return array<int, array>
     */
    public function createNodes(): array
    {
        $panel = Filament::getCurrentPanel();

        if ($panel === null) {
            return [];
        }

        $nodes = [];

        foreach ($panel->getResources() as $resource) {
            $pages = $resource::getPages();

            if (! isset($pages['create'])) {
                continue;
            }

            try {
                if (! $pages['create']->getPage()::canAccess()) {
                    continue;
                }

                $url = $resource::getUrl('create');
            } catch (Throwable) {
                continue;
            }

            $label = Str::ucfirst($resource::getModelLabel());

            try {
                $indexUrl = $resource::getUrl('index');
            } catch (Throwable) {
                $indexUrl = null;
            }

            $nodes[] = [
                'id'        => 'create.'.md5($resource),
                'label'     => __('support::quick-navigation.new', ['label' => $label]),
                'icon'      => 'heroicon-o-plus-circle',
                'url'       => $url,
                'indexUrl'  => $indexUrl,
                'clusterId' => null,
                'keywords'  => 'create new '.Str::lower($label),
            ];
        }

        return $nodes;
    }

    /**
     * Globally-searchable resource classes belonging to a cluster.
     *
     * @return array<int, class-string>
     */
    public function clusterResources(string $clusterId): array
    {
        $cluster = $this->clusterClass($clusterId);

        if ($cluster === null) {
            return [];
        }

        return array_values(array_filter(
            $cluster::getClusteredComponents(),
            fn (string $component): bool => is_subclass_of($component, Resource::class),
        ));
    }

    /**
     * Flattened nodes (top-level + cluster children) for search.
     *
     * @return array<int, array{group: string, section: ?string, node: array}>
     */
    public function flat(): array
    {
        $flat = [];

        foreach ($this->groups() as $group) {
            foreach ($group['nodes'] as $node) {
                if ($node['clusterId'] !== null) {
                    $children = $this->clusterChildren($node['clusterId']);

                    foreach ($children['nodes'] ?? [] as $child) {
                        $flat[] = ['group' => $group['label'], 'section' => $children['label'], 'node' => $child];
                    }

                    continue;
                }

                $flat[] = ['group' => $group['label'], 'section' => null, 'node' => $node];
            }
        }

        return $flat;
    }

    protected function componentNode(string $component): ?array
    {
        try {
            if (is_subclass_of($component, Resource::class)) {
                if (! $component::canAccess()) {
                    return null;
                }

                return [
                    'id'        => 'cnode.'.md5($component),
                    'label'     => Str::ucfirst($component::getPluralModelLabel()),
                    'icon'      => $component::getNavigationIcon(),
                    'url'       => $component::getUrl('index'),
                    'sort'      => $component::getNavigationSort(),
                    'clusterId' => null,
                ];
            }

            if (is_subclass_of($component, Page::class)) {
                if (! $component::canAccess()) {
                    return null;
                }

                return [
                    'id'        => 'cnode.'.md5($component),
                    'label'     => $component::getNavigationLabel(),
                    'icon'      => method_exists($component, 'getNavigationIcon') ? $component::getNavigationIcon() : null,
                    'url'       => $component::getUrl(),
                    'sort'      => method_exists($component, 'getNavigationSort') ? $component::getNavigationSort() : null,
                    'clusterId' => null,
                ];
            }
        } catch (Throwable) {
            return null;
        }

        return null;
    }

    /**
     * @return array<string, array{class: string, id: string, label: string}>
     */
    protected function clusterMap(): array
    {
        if ($this->clusters !== null) {
            return $this->clusters;
        }

        $panel = Filament::getCurrentPanel();
        $map = [];

        foreach ($panel?->getClusters() ?? [] as $cluster) {
            try {
                $url = $cluster::getUrl();
            } catch (Throwable) {
                continue;
            }

            $map[$this->path($url)] = [
                'class' => $cluster,
                'id'    => 'cluster.'.md5($cluster),
                'label' => $cluster::getNavigationLabel(),
            ];
        }

        return $this->clusters = $map;
    }

    protected function clusterClass(string $clusterId): ?string
    {
        foreach ($this->clusterMap() as $cluster) {
            if ($cluster['id'] === $clusterId) {
                return $cluster['class'];
            }
        }

        return null;
    }

    protected function path(string $url): string
    {
        return (string) parse_url($url, PHP_URL_PATH);
    }
}
