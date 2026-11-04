<?php

namespace Webkul\Crm\Filament\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Throwable;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Services\SMS\SMSSender;
use Webkul\Crm\Settings\SMSSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageSMS extends SettingsPage
{
    use HasCRMClusterNavigation, HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $slug = 'sms/manage-sms';

    protected static ?int $navigationSort = 0;

    protected static string $settings = SMSSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_crm_manage_s_m_s';
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-sms.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-sms.title');
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/settings/pages/manage-sms.title');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('crm::filament/clusters/settings/pages/manage-sms.title'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTestSMS')
                ->label(__('crm::filament/clusters/settings/pages/manage-sms.actions.test.label'))
                ->icon('heroicon-o-device-phone-mobile')
                ->color('gray')
                ->visible(fn (): bool => app(SMSSender::class)->isConfigured())
                ->schema([
                    TextInput::make('number')
                        ->label(__('crm::filament/clusters/settings/pages/manage-sms.actions.test.number'))
                        ->helperText(__('crm::filament/sms.form.international-helper'))
                        ->tel()
                        ->required()
                        ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                            if (! SMSSender::normalize($value, null)) {
                                $fail(__('crm::filament/sms.errors.invalid-number'));
                            }
                        }),
                ])
                ->action(function (array $data): void {
                    $number = SMSSender::normalize($data['number'], null);

                    try {
                        app(SMSSender::class)->send($number, __('crm::filament/clusters/settings/pages/manage-sms.actions.test.message'));
                    } catch (Throwable $exception) {
                        Notification::make()
                            ->danger()
                            ->title(__('crm::filament/clusters/settings/pages/manage-sms.actions.test.failed'))
                            ->body($exception->getMessage())
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->success()
                        ->title(__('crm::filament/clusters/settings/pages/manage-sms.actions.test.sent', ['number' => '+'.$number]))
                        ->send();
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_active')
                    ->label(__('crm::filament/clusters/settings/pages/manage-sms.form.active'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-sms.form.active-helper'))
                    ->live()
                    ->columnSpanFull(),
                TextInput::make('auth_key')
                    ->label(__('crm::filament/clusters/settings/pages/manage-sms.form.auth-key'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-sms.form.auth-key-helper'))
                    ->password()
                    ->revealable()
                    ->formatStateUsing(fn (): ?string => null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (Get $get): bool => (bool) $get('is_active') && blank(app(SMSSettings::class)->auth_key))
                    ->autocomplete('new-password'),
                TextInput::make('sender_id')
                    ->label(__('crm::filament/clusters/settings/pages/manage-sms.form.sender-id'))
                    ->helperText(__('crm::filament/clusters/settings/pages/manage-sms.form.sender-id-helper'))
                    ->required(fn (Get $get): bool => (bool) $get('is_active'))
                    ->maxLength(11),
                Select::make('route')
                    ->label(__('crm::filament/clusters/settings/pages/manage-sms.form.route'))
                    ->options([
                        '4' => __('crm::filament/clusters/settings/pages/manage-sms.form.routes.transactional'),
                        '1' => __('crm::filament/clusters/settings/pages/manage-sms.form.routes.promotional'),
                    ])
                    ->selectablePlaceholder(false)
                    ->required(),
            ])
            ->columns(2);
    }
}
