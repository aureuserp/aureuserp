<?php

namespace Webkul\Website\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Website\Enums\ContactFormAction;
use Webkul\Website\Mail\ContactFormMail;
use Webkul\Website\Settings\ContactSettings;

class ContactFormHandler
{
    public const FIELDS = ['name', 'phone', 'email', 'company', 'subject', 'question'];

    public const MULTILINE_FIELDS = ['question'];

    public static function crmAvailable(): bool
    {
        return Package::isPluginInstalled('crm') && class_exists('Webkul\\Crm\\Models\\Lead');
    }

    public static function crmUsesLeads(): bool
    {
        return static::crmAvailable() && (bool) app('Webkul\\Crm\\Settings\\CRMSettings')->use_leads;
    }

    public static function availableActions(): array
    {
        return array_values(array_filter([
            ContactFormAction::EMAIL,
            static::crmUsesLeads() ? ContactFormAction::LEAD : null,
            static::crmAvailable() ? ContactFormAction::OPPORTUNITY : null,
        ]));
    }

    public static function actionOptions(): array
    {
        return collect(static::availableActions())
            ->mapWithKeys(fn (ContactFormAction $action): array => [$action->value => $action->getLabel()])
            ->all();
    }

    public static function action(): ContactFormAction
    {
        $action = ContactFormAction::tryFrom((string) app(ContactSettings::class)->contact_form_action);

        if ($action === ContactFormAction::LEAD
            && static::crmAvailable()
            && ! static::crmUsesLeads()) {
            return ContactFormAction::OPPORTUNITY;
        }

        return in_array($action, static::availableActions(), true) ? $action : ContactFormAction::EMAIL;
    }

    public static function recipient(): ?string
    {
        return config('mail.from.address');
    }

    public static function sanitize(array $data): array
    {
        $clean = [];

        foreach (self::FIELDS as $field) {
            $value = is_scalar($data[$field] ?? null) ? (string) $data[$field] : '';

            $value = str_replace(["\r\n", "\r"], "\n", $value);
            $value = strip_tags(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $value = preg_replace('/[\x{0000}-\x{0008}\x{000B}\x{000C}\x{000E}-\x{001F}\x{007F}\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $value) ?? '';
            $value = in_array($field, self::MULTILINE_FIELDS, true)
                ? preg_replace("/\n{3,}/", "\n\n", $value)
                : preg_replace('/\s+/u', ' ', $value);

            $clean[$field] = filled($value = trim((string) $value)) ? $value : null;
        }

        $clean['email'] = filter_var($clean['email'], FILTER_VALIDATE_EMAIL) ?: null;

        if (blank($clean['name']) || blank($clean['email']) || blank($clean['phone']) || blank($clean['subject']) || blank($clean['question'])) {
            throw new InvalidArgumentException('Contact form submission is empty after sanitizing.');
        }

        return $clean;
    }

    public function handle(array $data): ?Model
    {
        $data = static::sanitize($data);

        $action = static::action();

        return match ($action) {
            ContactFormAction::LEAD, ContactFormAction::OPPORTUNITY => $this->createLead($data, $action),
            ContactFormAction::EMAIL                                => $this->sendEmail($data),
        };
    }

    protected function sendEmail(array $data): null
    {
        if (filled($recipient = static::recipient())) {
            $companyEmail = Company::query()->where('is_active', true)->orderBy('id')->value('email');

            Mail::to($recipient)
                ->when(filled($companyEmail) && strcasecmp($companyEmail, $recipient) !== 0, fn ($mail) => $mail->cc($companyEmail))
                ->send(new ContactFormMail($data));
        }

        return null;
    }

    protected function createLead(array $data, ContactFormAction $action): Model
    {
        $leadClass = 'Webkul\\Crm\\Models\\Lead';

        $lead = $leadClass::query()->create([
            'type'         => $action->value,
            'name'         => $data['subject'],
            'contact_name' => $data['name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'],
            'company_name' => $data['company'] ?? null,
            'description'  => '<p>'.nl2br(e($data['question'])).'</p>',
            'company_id'   => Company::query()->where('is_active', true)->orderBy('id')->value('id'),
            'user_id'      => null,
            'creator_id'   => null,
        ]);

        $lead->addMessage([
            'type'       => 'note',
            'body'       => '<p>'.e(__('website::filament/customer/contact-us.lead-note')).'</p>',
            'company_id' => $lead->company_id,
        ]);

        return $lead;
    }
}
