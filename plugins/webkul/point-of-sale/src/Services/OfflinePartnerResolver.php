<?php

namespace Webkul\PointOfSale\Services;

use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\PointOfSale\Models\Config;

class OfflinePartnerResolver
{
    public function resolve(array $payload, Config $config): ?int
    {
        if (! empty($payload['partner_id']) && is_numeric($payload['partner_id'])) {
            return (int) $payload['partner_id'];
        }

        $draft = $payload['partner'] ?? null;

        if (! is_array($draft) || blank($draft['name'] ?? null)) {
            return null;
        }

        $existing = $this->findExisting($draft, $config);

        if ($existing) {
            return $existing->id;
        }

        return Partner::create([
            'account_type' => AccountType::INDIVIDUAL,
            'sub_type'     => 'customer',
            'name'         => $draft['name'],
            'email'        => $draft['email'] ?? null,
            'phone'        => $draft['phone'] ?? null,
            'mobile'       => $draft['mobile'] ?? null,
            'street1'      => $draft['street1'] ?? null,
            'city'         => $draft['city'] ?? null,
            'zip'          => $draft['zip'] ?? null,
            'company_id'   => $config->company_id,
        ])->id;
    }

    protected function findExisting(array $draft, Config $config): ?Partner
    {
        $query = Partner::query()->where(fn ($builder) => $builder
            ->where('company_id', $config->company_id)
            ->orWhereNull('company_id'));

        if (filled($draft['email'] ?? null)) {
            return $query->where('email', $draft['email'])->first();
        }

        if (filled($draft['phone'] ?? null)) {
            return $query->where('phone', $draft['phone'])->first();
        }

        return $query->where('name', $draft['name'])->first();
    }
}
