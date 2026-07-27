<?php

namespace Webkul\Support\Filament\Pages;

use Filament\Infolists\Components\ViewEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Webkul\Support\Enums\NavigationGroup;

class Help extends Page
{
    protected string $view = 'support::pages.help';

    protected static ?string $slug = 'help';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('support::filament/pages/help.navigation.label');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Help;
    }

    public function getTitle(): string
    {
        return __('support::filament/pages/help.title');
    }

    public function getHeading(): string
    {
        return __('support::filament/pages/help.heading');
    }

    public function getSubheading(): ?string
    {
        return __('support::filament/pages/help.subheading');
    }

    public function servicesInfolist(Schema $schema): Schema
    {
        return $schema->components([
            $this->cardsGrid($this->services()),
        ]);
    }

    public function resourcesInfolist(Schema $schema): Schema
    {
        return $schema->components([
            $this->cardsGrid($this->resources()),
        ]);
    }

    protected function cardsGrid(array $cards): Grid
    {
        return Grid::make([
            'default' => 1,
            'md'      => 2,
            'xl'      => 3,
        ])->schema(
            array_map(
                fn (array $card, int $index): ViewEntry => ViewEntry::make("card_{$index}")
                    ->hiddenLabel()
                    ->view('support::pages.partials.help-card', ['card' => $card]),
                $cards,
                array_keys($cards),
            )
        );
    }

    protected function services(): array
    {
        return [
            [
                'icon'        => 'heroicon-o-cloud',
                'title'       => __('support::filament/pages/help.services.cloud.title'),
                'description' => __('support::filament/pages/help.services.cloud.description'),
                'url'         => 'https://aureuserp.com/cloud-hosting',
                'button'      => __('support::filament/pages/help.services.cloud.button'),
            ],
            [
                'icon'        => 'heroicon-o-lifebuoy',
                'title'       => __('support::filament/pages/help.services.support.title'),
                'description' => __('support::filament/pages/help.services.support.description'),
                'url'         => 'https://aureuserp.com/erp-support-maintenance-services',
                'button'      => __('support::filament/pages/help.services.support.button'),
            ],
            [
                'icon'        => 'heroicon-o-key',
                'title'       => __('support::filament/pages/help.services.paid.title'),
                'description' => __('support::filament/pages/help.services.paid.description'),
                'url'         => 'https://aureuserp.com/custom-erp-development',
                'button'      => __('support::filament/pages/help.services.paid.button'),
            ],
        ];
    }

    protected function resources(): array
    {
        return [
            [
                'icon'        => 'heroicon-o-puzzle-piece',
                'title'       => __('support::filament/pages/help.resources.extensions.title'),
                'description' => __('support::filament/pages/help.resources.extensions.description'),
                'url'         => 'https://store.webkul.com/catalogsearch/result/?cat=All+Categories&q=AureusERP',
                'button'      => __('support::filament/pages/help.resources.extensions.button'),
            ],
            [
                'icon'        => 'heroicon-o-document-text',
                'title'       => __('support::filament/pages/help.resources.docs.title'),
                'description' => __('support::filament/pages/help.resources.docs.description'),
                'url'         => 'https://devdocs.aureuserp.com',
                'button'      => __('support::filament/pages/help.resources.docs.button'),
            ],
            [
                'icon'        => 'heroicon-o-book-open',
                'title'       => __('support::filament/pages/help.resources.guide.title'),
                'description' => __('support::filament/pages/help.resources.guide.description'),
                'url'         => 'https://docs.aureuserp.com',
                'button'      => __('support::filament/pages/help.resources.guide.button'),
            ],
        ];
    }
}
