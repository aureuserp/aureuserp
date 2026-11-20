<?php

namespace Webkul\Website\Filament\Customer\Pages;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;
use Webkul\Support\Models\Company;
use Webkul\Website\Services\ContactFormHandler;
use Webkul\Website\Settings\ContactSettings;

class ContactUs extends Page
{
    public const MAX_ATTEMPTS = 5;

    public const HONEYPOT = 'hp_check_7f3a';

    public const NO_MARKUP_PATTERN = '/<\s*[a-z\/!?]|javascript\s*:|vbscript\s*:|data\s*:\s*text\/html|\bon[a-z]+\s*=/i';

    public const PHONE_PATTERN = '/^\+?[0-9\s\-().\/]{6,30}$/';

    protected static ?string $slug = 'contact-us';

    protected static ?int $navigationSort = 99;

    protected string $view = 'website::filament.customer.pages.contact-us';

    public ?array $data = [];

    public static function isEnabled(): bool
    {
        return (bool) app(ContactSettings::class)->contact_form_enabled;
    }

    public static function canAccess(): bool
    {
        return static::isEnabled();
    }

    public static function getNavigationLabel(): string
    {
        return __('website::filament/customer/contact-us.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('website::filament/customer/contact-us.title');
    }

    public function mount(): void
    {
        abort_unless(static::isEnabled(), 404);

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $label = fn (string $key): string => __('website::filament/customer/contact-us.fields.'.$key);

        $messages = [
            'not_regex' => __('website::filament/customer/contact-us.validation.no-markup'),
            'regex'     => __('website::filament/customer/contact-us.validation.phone'),
        ];

        return $schema
            ->statePath('data')
            ->components([
                Grid::make(['default' => 1, 'md' => 2])
                    ->schema([
                        TextInput::make('name')
                            ->label($label('name'))
                            ->required()
                            ->maxLength(255)
                            ->notRegex(self::NO_MARKUP_PATTERN)
                            ->validationMessages($messages),
                        TextInput::make('phone')
                            ->label($label('phone'))
                            ->tel()
                            ->required()
                            ->maxLength(30)
                            ->regex(self::PHONE_PATTERN)
                            ->validationMessages($messages),
                        TextInput::make('email')
                            ->label($label('email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->notRegex(self::NO_MARKUP_PATTERN)
                            ->validationMessages($messages),
                        TextInput::make('company')
                            ->label($label('company'))
                            ->maxLength(255)
                            ->notRegex(self::NO_MARKUP_PATTERN)
                            ->validationMessages($messages),
                        TextInput::make('subject')
                            ->label($label('subject'))
                            ->required()
                            ->maxLength(255)
                            ->notRegex(self::NO_MARKUP_PATTERN)
                            ->validationMessages($messages)
                            ->columnSpanFull(),
                        Textarea::make('question')
                            ->label($label('question'))
                            ->required()
                            ->rows(8)
                            ->maxLength(5000)
                            ->notRegex(self::NO_MARKUP_PATTERN)
                            ->validationMessages($messages)
                            ->columnSpanFull(),
                        TextInput::make(self::HONEYPOT)
                            ->hiddenLabel()
                            ->autocomplete('one-time-code')
                            ->extraInputAttributes([
                                'tabindex'         => '-1',
                                'aria-hidden'      => 'true',
                                'data-lpignore'    => 'true',
                                'data-1p-ignore'   => 'true',
                                'data-bwignore'    => 'true',
                                'data-form-type'   => 'other',
                            ])
                            ->extraFieldWrapperAttributes(['style' => 'position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden;'])
                            ->dehydrated(),
                    ]),
            ]);
    }

    public function submit(): void
    {
        abort_unless(static::isEnabled(), 404);

        $data = $this->form->getState();

        if (filled($data[self::HONEYPOT] ?? null)) {
            Log::notice('Website Contact Us submission dropped by the spam trap.', ['ip' => request()->ip(), 'email' => $data['email'] ?? null]);

            $this->sent();

            return;
        }

        $key = 'website-contact-us:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            Notification::make()
                ->danger()
                ->title(__('website::filament/customer/contact-us.notifications.too-many', ['minutes' => ceil(RateLimiter::availableIn($key) / 60)]))
                ->send();

            return;
        }

        RateLimiter::hit($key, 600);

        try {
            app(ContactFormHandler::class)->handle(collect($data)->except(self::HONEYPOT)->all());
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->danger()
                ->title(__('website::filament/customer/contact-us.notifications.failed'))
                ->send();

            return;
        }

        $this->sent();
    }

    public function getCompany(): ?Company
    {
        return Company::query()->where('is_active', true)->orderBy('id')->first();
    }

    protected function sent(): void
    {
        Notification::make()
            ->success()
            ->title(__('website::filament/customer/contact-us.notifications.sent.title'))
            ->body(__('website::filament/customer/contact-us.notifications.sent.body'))
            ->send();

        $this->form->fill();
    }
}
