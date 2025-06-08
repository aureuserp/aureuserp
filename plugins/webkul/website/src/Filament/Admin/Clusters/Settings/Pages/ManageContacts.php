<?php

namespace Webkul\Website\Filament\Admin\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Website\Settings\ContactSettings;

class ManageContacts extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $slug = 'website/manage-contacts';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 5;

    protected static string $settings = ContactSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('website::filament/admin/clusters/settings/pages/manage-contacts.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('website::filament/admin/clusters/settings/pages/manage-contacts.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('website::filament/admin/clusters/settings/pages/manage-contacts.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.contacts.title'))
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.contacts.fields.email'))
                            ->placeholder('support@example.com')
                            ->email(),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.contacts.fields.phone'))
                            ->placeholder('+1234567890')
                            ->tel(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.title'))
                    ->schema([
                        Forms\Components\TextInput::make('twitter')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.fields.twitter'))
                            ->placeholder('username')
                            ->prefix('https://x.com/'),
                        Forms\Components\TextInput::make('facebook')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.fields.facebook'))
                            ->placeholder('username')
                            ->prefix('https://facebook.com/'),
                        Forms\Components\TextInput::make('instagram')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.fields.instagram'))
                            ->placeholder('username')
                            ->prefix('https://instagram.com/'),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.fields.whatsapp'))
                            ->placeholder('username')
                            ->prefix('https://wa.me/'),
                        Forms\Components\TextInput::make('youtube')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.fields.youtube'))
                            ->placeholder('username')
                            ->prefix('https://youtube.com/'),
                        Forms\Components\TextInput::make('linkedin')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.fields.linkedin'))
                            ->placeholder('username')
                            ->prefix('https://linkedin.com/'),
                        Forms\Components\TextInput::make('pinterest')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.fields.pinterest'))
                            ->placeholder('username')
                            ->prefix('https://pinterest.com/'),
                        Forms\Components\TextInput::make('tiktok')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.fields.tiktok'))
                            ->placeholder('username')
                            ->prefix('https://tiktok.com/@'),
                        Forms\Components\TextInput::make('github')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.fields.github'))
                            ->placeholder('username')
                            ->prefix('https://github.com/'),
                        Forms\Components\TextInput::make('slack')
                            ->label(__('website::filament/admin/clusters/settings/pages/manage-contacts.form.sections.social-links.fields.slack'))
                            ->placeholder('username')
                            ->prefix('https://slack.com/'),
                    ])
                    ->columns(2),
            ]);
    }
}
