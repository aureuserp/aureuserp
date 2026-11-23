<?php

namespace Webkul\Support\Filament\Pages;

use Filament\Pages\Page;
use Filament\Panel;

class Home extends Page
{
    protected string $view = 'support::pages.home';

    protected static ?string $slug = 'home';

    protected static bool $shouldRegisterNavigation = false;

    public static function getRoutePath(Panel $panel): string
    {
        return '/';
    }

    public function getTitle(): string
    {
        return __('support::filament/pages/home.title');
    }

    public function getHeading(): string
    {
        return '';
    }
}
