<?php

namespace Webkul\Website;

use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Collection;
use ReflectionClass;
use Webkul\Support\Package;
use Webkul\Website\Filament\Customer\Auth\Login;
use Webkul\Website\Filament\Customer\Auth\PasswordReset\RequestPasswordReset;
use Webkul\Website\Filament\Customer\Auth\PasswordReset\ResetPassword;
use Webkul\Website\Filament\Customer\Auth\Register;
use Webkul\Website\Filament\Customer\Clusters\Account;
use Webkul\Website\Filament\Customer\Resources\PageResource;
use Webkul\Website\Models\Page;
use Webkul\Website\Settings\ContactSettings;

class WebsitePlugin implements Plugin
{
    public function getId(): string
    {
        return 'website';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel
            ->when($panel->getId() == 'customer', function (Panel $panel) {
                $panel
                    ->login(Login::class)
                    ->registration(Register::class)
                    ->passwordReset(RequestPasswordReset::class, ResetPassword::class)
                    ->discoverResources(in: $this->getPluginBasePath('/Filament/Customer/Resources'), for: 'Webkul\\Website\\Filament\\Customer\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Customer/Pages'), for: 'Webkul\\Website\\Filament\\Customer\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Customer/Clusters'), for: 'Webkul\\Website\\Filament\\Customer\\Clusters')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Customer/Widgets'), for: 'Webkul\\Website\\Filament\\Customer\\Widgets')
                    ->userMenuItems([
                        'my_account' => Action::make('my_account')->label(fn () => __('website::filament/app.navigation.account.label'))
                            ->url(fn (): string => Account::getUrl())
                            ->sort(2)
                            ->visible(fn (): bool => (bool) count(Account::getClusteredComponents())),
                    ])
                    ->navigationItems($this->getNavigationItems()->toArray())
                    ->renderHook(
                        PanelsRenderHook::TOPBAR_END,
                        fn (): string => view('website::filament.customer.header.auth-links', [
                            'navigationItems' => $this->getTopNavigationItems(),
                        ])->render(),
                    )
                    ->renderHook(
                        PanelsRenderHook::FOOTER,
                        fn (): string => view('website::filament.customer.footer.index', [
                            'navigationItems' => $this->getFooterNavigationItems(),
                            'contacts'        => $this->getContacts(),
                            'socialLinks'     => $this->getSocialLinks(),
                        ])->render(),
                    );
            })
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel
                    ->discoverResources(in: $this->getPluginBasePath('/Filament/Admin/Resources'), for: 'Webkul\\Website\\Filament\\Admin\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Admin/Pages'), for: 'Webkul\\Website\\Filament\\Admin\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Admin/Clusters'), for: 'Webkul\\Website\\Filament\\Admin\\Clusters')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Admin/Widgets'), for: 'Webkul\\Website\\Filament\\Admin\\Widgets');
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }

    protected function getPluginBasePath($path = null): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName()).($path ?? '');
    }

    protected function getTopNavigationItems(): Collection
    {
        return new Collection([
            NavigationItem::make('Login')
                ->label(fn () => __('website::filament/app.navigation.top.login'))
                ->url(filament()->getLoginUrl())
                ->visible(! filament()->auth()->check()),
            NavigationItem::make('Register')
                ->label(fn () => __('website::filament/app.navigation.top.register'))
                ->url(filament()->getRegistrationUrl())
                ->visible(! filament()->auth()->check()),
        ]);
    }

    protected function getNavigationItems(): Collection
    {
        $navigationItems = new Collection;

        $pages = Page::where([
            'is_header_visible' => true,
            'is_published'      => true,
        ])->get();

        $pages->each(function ($page) use ($navigationItems) {
            $navigationItems->push(
                NavigationItem::make($page->slug)
                    ->label($page->title)
                    ->url(fn (): string => PageResource::getUrl('view', ['record' => $page->slug]))
                    ->isActiveWhen(function () use ($page) {
                        if (! request()->routeIs(PageResource::getRouteBaseName().'.view')) {
                            return false;
                        }

                        return request('record') === $page->slug;
                    })
            );
        });

        return $navigationItems;
    }

    protected function getFooterNavigationItems(): Collection
    {
        $navigationItems = new Collection([
            NavigationItem::make('Home')
                ->label(fn () => __('website::filament/app.navigation.footer.home'))
                ->url('/'),
        ]);

        $pages = Page::where([
            'is_footer_visible' => true,
            'is_published'      => true,
        ])->get();

        $pages->each(function ($page) use ($navigationItems) {
            $navigationItems->push(
                NavigationItem::make($page->slug)
                    ->label($page->title)
                    ->url(fn (): string => PageResource::getUrl('view', ['record' => $page->slug]))
                    ->isActiveWhen(function () use ($page) {
                        if (! request()->routeIs(PageResource::getRouteBaseName().'.view')) {
                            return false;
                        }

                        return request('record') === $page->slug;
                    })
            );
        });

        return $navigationItems;
    }

    protected function getContacts(): array
    {
        $contacts = [];

        $contactSettings = app(ContactSettings::class);

        if ($contactSettings->email) {
            $contacts['email'] = $contactSettings->email;
        }

        if ($contactSettings->phone) {
            $contacts['phone'] = $contactSettings->phone;
        }

        return $contacts;
    }

    protected function getSocialLinks(): Collection
    {
        $socialLinks = new Collection;

        $contactSettings = app(ContactSettings::class);

        if ($contactSettings->facebook) {
            $socialLinks->push(
                NavigationItem::make('Facebook')
                    ->label(fn () => __('website::filament/app.navigation.social.facebook'))
                    ->url('https://faceboox.com/'.$contactSettings->facebook)
                    ->icon(fn (): string => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"></path></svg>')
            );
        }

        if ($contactSettings->twitter) {
            $socialLinks->push(
                NavigationItem::make('Twitter')
                    ->label(fn () => __('website::filament/app.navigation.social.twitter'))
                    ->url('https://twitter.com/'.$contactSettings->twitter)
                    ->icon(fn (): string => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg>')
            );
        }

        if ($contactSettings->instagram) {
            $socialLinks->push(
                NavigationItem::make('Instagram')
                    ->label(fn () => __('website::filament/app.navigation.social.instagram'))
                    ->url('https://instagram.com/'.$contactSettings->instagram)
                    ->icon(fn (): string => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm5.888 14.12c-.23.007-.461.007-.691.007-1.28 0-2.561-.137-3.779-.407-1.325-.296-2.604-.854-3.686-1.674a8.472 8.472 0 01-2.307-2.64 8.081 8.081 0 01-1.174-3.05 9.52 9.52 0 01-.07-2.301c.072-.83.283-1.653.631-2.404a7.63 7.63 0 011.922-2.416A8.57 8.57 0 0111.55 2.21a9.98 9.98 0 012.5-.252c.83.039 1.648.195 2.432.457a8.89 8.89 0 012.896 1.491c1.527 1.186 2.755 2.682 3.375 4.58.418 1.23.57 2.57.445 3.878-.118 1.318-.51 2.575-1.153 3.646-.757 1.255-1.76 2.255-2.92 2.996-.823.497-1.75.778-2.695.897-.258.033-.517.05-.777.05-.258 0-.516-.017-.775-.05zm.705-13.45a7.29 7.29 0 00-3.89-.607c-1.596.178-3.137.981-4.297 2.175a7.185 7.185 0 00-1.88 3.22 7.587 7.587 0 00-.107 2.79c.16 1.3.703 2.527 1.546 3.525.705.831 1.625 1.474 2.648 1.845.772.281 1.596.402 2.408.344 1.1-.077 2.143-.51 2.98-1.196a6.423 6.423 0 001.91-2.626c.394-.92.576-1.947.52-2.962a6.332 6.332 0 00-.709-2.61 6.822 6.822 0 00-1.13-1.701z"></path></svg>')
            );
        }

        if ($contactSettings->youtube) {
            $socialLinks->push(
                NavigationItem::make('YouTube')
                    ->label(fn () => __('website::filament/app.navigation.social.youtube'))
                    ->url('https://youtube.com/'.$contactSettings->youtube)
                    ->icon(fn (): string => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"></path></svg>')
            );
        }

        if ($contactSettings->linkedin) {
            $socialLinks->push(
                NavigationItem::make('LinkedIn')
                    ->label(fn () => __('website::filament/app.navigation.social.linkedin'))
                    ->url('https://linkedin.com/in/'.$contactSettings->linkedin)
                    ->icon(fn (): string => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>')
            );
        }

        if ($contactSettings->pinterest) {
            $socialLinks->push(
                NavigationItem::make('Pinterest')
                    ->label(fn () => __('website::filament/app.navigation.social.pinterest'))
                    ->url('https://pinterest.com/'.$contactSettings->pinterest)
                    ->icon(fn (): string => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg>')
            );
        }

        if ($contactSettings->tiktok) {
            $socialLinks->push(
                NavigationItem::make('TikTok')
                    ->label(fn () => __('website::filament/app.navigation.social.tiktok'))
                    ->url('https://tiktok.com/@'.$contactSettings->tiktok)
                    ->icon(fn (): string => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>')
            );
        }

        if ($contactSettings->github) {
            $socialLinks->push(
                NavigationItem::make('GitHub')
                    ->label(fn () => __('website::filament/app.navigation.social.github'))
                    ->url('https://github.com/'.$contactSettings->github)
                    ->icon(fn (): string => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>')
            );
        }

        if ($contactSettings->whatsapp) {
            $socialLinks->push(
                NavigationItem::make('WhatsApp')
                    ->url('https://wa.me/'.$contactSettings->whatsapp)
                    ->label(fn () => __('website::filament/app.navigation.social.whatsapp'))
                    ->icon(fn (): string => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>')
            );
        }

        if ($contactSettings->slack) {
            $socialLinks->push(
                NavigationItem::make('Slack')
                    ->url('https://slack.com/'.$contactSettings->slack)
                    ->label(fn () => __('website::filament/app.navigation.social.slack'))
                    ->icon(fn (): string => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.526 2.526 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z"/></svg>')
            );
        }

        return $socialLinks;
    }
}
