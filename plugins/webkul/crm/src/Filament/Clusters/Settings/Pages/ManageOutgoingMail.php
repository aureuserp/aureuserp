<?php

namespace Webkul\Crm\Filament\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Mail\LeadMessageMail;
use Webkul\Crm\Services\Mail\OutgoingMailer;
use Webkul\Crm\Settings\OutgoingMailSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageOutgoingMail extends SettingsPage
{
    use HasCRMClusterNavigation, HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $slug = 'email/manage-outgoing-mail';

    protected static ?int $navigationSort = 0;

    protected static string $settings = OutgoingMailSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_crm_manage_outgoing_mail';
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/mail-server.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-outgoing-mail.title');
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-outgoing-mail.title');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('crm::filament/clusters/settings/pages/manage-outgoing-mail.title'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTestEmail')
                ->label(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.actions.test.label'))
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->visible(fn (): bool => app(OutgoingMailer::class)->usesCustomServer() && filled(Auth::user()?->email))
                ->action(function (): void {
                    $user = Auth::user();

                    try {
                        app(OutgoingMailer::class)->send(
                            $user->email,
                            $user->name,
                            new LeadMessageMail(
                                __('crm::filament/clusters/settings/pages/manage-outgoing-mail.actions.test.subject'),
                                __('crm::filament/clusters/settings/pages/manage-outgoing-mail.actions.test.body'),
                            ),
                        );
                    } catch (Throwable $exception) {
                        Notification::make()
                            ->danger()
                            ->title(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.actions.test.failed'))
                            ->body(mb_substr($exception->getMessage(), 0, 300))
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->success()
                        ->title(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.actions.test.sent', ['email' => $user->email]))
                        ->send();
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_active')
                    ->label(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.active'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.active-helper'))
                    ->live(),
                TextInput::make('host')
                    ->label(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.host'))
                    ->placeholder('smtp.gmail.com')
                    ->required(fn (Get $get): bool => (bool) $get('is_active'))
                    ->maxLength(255),
                Select::make('encryption')
                    ->label(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.encryption'))
                    ->options([
                        'tls'  => __('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.encryptions.tls'),
                        'ssl'  => __('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.encryptions.ssl'),
                        'none' => __('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.encryptions.none'),
                    ])
                    ->selectablePlaceholder(false)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('port', match ($state) {
                        'ssl'   => 465,
                        'none'  => 25,
                        default => 587,
                    })),
                TextInput::make('port')
                    ->label(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.port'))
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(65535)
                    ->required(),
                TextInput::make('username')
                    ->label(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.username'))
                    ->maxLength(255)
                    ->autocomplete('off'),
                TextInput::make('password')
                    ->label(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.password'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.password-helper'))
                    ->password()
                    ->revealable()
                    ->formatStateUsing(fn (): ?string => null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->autocomplete('new-password'),
                TextInput::make('from_address')
                    ->label(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.from-address'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.from-address-helper'))
                    ->email()
                    ->maxLength(255),
                TextInput::make('from_name')
                    ->label(__('crm::filament/clusters/settings/pages/manage-outgoing-mail.form.from-name'))
                    ->maxLength(255),
            ])
            ->columns(2);
    }
}
