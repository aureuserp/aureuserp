<?php

namespace Webkul\Crm\Services\SMS;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Webkul\Crm\Enums\SMSEncoding;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Settings\SMSSettings;

class SMSSender
{
    public const GSM7_CHARACTERS = "@£\$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !\"#¤%&'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà";

    public const GSM7_EXTENDED_CHARACTERS = "^{}\\[~]|€\f";

    public function __construct(protected SMSSettings $settings) {}

    public function usesCustomGateway(): bool
    {
        return $this->settings->is_active && filled($this->settings->auth_key);
    }

    public function isConfigured(): bool
    {
        return filled($this->authKey());
    }

    public function send(string $number, string $message): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException(__('crm::filament/sms.errors.not-configured'));
        }

        try {
            $response = Http::asForm()->timeout(30)->post('https://api.msg91.com/api/sendhttp.php', [
                'authkey' => $this->authKey(),
                'mobiles' => $number,
                'message' => $message,
                'sender'  => $this->usesCustomGateway() ? $this->settings->sender_id : config('services.msg91.sender_id'),
                'route'   => $this->usesCustomGateway() ? $this->settings->route : config('services.msg91.route'),
                'unicode' => SMSEncoding::from(static::segments($message)['encoding'])->isUnicode() ? 1 : 0,
            ]);
        } catch (ConnectionException) {
            throw new RuntimeException(__('crm::filament/sms.errors.unreachable'));
        }

        if (! $response->successful()) {
            throw new RuntimeException(mb_substr($response->body(), 0, 300));
        }
    }

    protected function authKey(): ?string
    {
        return $this->usesCustomGateway() ? $this->settings->auth_key : config('services.msg91.auth_key');
    }

    public static function recipientFor(Lead $lead): ?string
    {
        return $lead->phone ?: ($lead->partner?->mobile ?: $lead->partner?->phone);
    }

    public static function phoneCodeFor(Lead $lead): ?string
    {
        return $lead->country?->phone_code ?: $lead->partner?->country?->phone_code;
    }

    public static function normalize(?string $number, ?string $phoneCode): ?string
    {
        $number = trim((string) $number);
        $digits = preg_replace('/\D+/', '', $number);

        $international = match (true) {
            $digits === ''                 => null,
            str_starts_with($number, '+')  => $digits,
            str_starts_with($digits, '00') => substr($digits, 2),
            filled($phoneCode)             => preg_replace('/\D+/', '', $phoneCode).ltrim($digits, '0'),
            default                        => null,
        };

        return $international !== null && strlen($international) >= 8 && strlen($international) <= 15 ? $international : null;
    }

    public static function segments(string $message): array
    {
        $characters = 0;
        $isGsm7 = true;

        foreach (mb_str_split($message) as $character) {
            if (str_contains(static::GSM7_CHARACTERS, $character)) {
                $characters++;
            } elseif (str_contains(static::GSM7_EXTENDED_CHARACTERS, $character)) {
                $characters += 2;
            } else {
                $isGsm7 = false;

                break;
            }
        }

        if (! $isGsm7) {
            $characters = mb_strlen($message);
        }

        $encoding = $isGsm7 ? SMSEncoding::GSM7 : SMSEncoding::UCS2;

        return [
            'characters' => $characters,
            'parts'      => match (true) {
                $characters === 0                               => 0,
                $characters <= $encoding->getSinglePartLength() => 1,
                default                                         => (int) ceil($characters / $encoding->getMultiPartLength()),
            },
            'encoding' => $encoding->value,
        ];
    }
}
