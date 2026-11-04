<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuotationBoardGroup: string implements HasLabel
{
    case SALESPERSON = 'user.name';

    case SALES_TEAM = 'team.name';

    case CUSTOMER = 'partner.name';

    case SOURCE = 'utmSource.name';

    case MEDIUM = 'medium.name';

    case COMPANY = 'company.name';

    case STATUS = 'state';

    public static function fromGrouping(?string $grouping): ?self
    {
        return self::tryFrom((string) str((string) $grouping)->before(':'));
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::SALESPERSON => __('crm::enums/quotation-board-group.salesperson'),
            self::SALES_TEAM  => __('crm::enums/quotation-board-group.sales-team'),
            self::CUSTOMER    => __('crm::enums/quotation-board-group.customer'),
            self::SOURCE      => __('crm::enums/quotation-board-group.source'),
            self::MEDIUM      => __('crm::enums/quotation-board-group.medium'),
            self::COMPANY     => __('crm::enums/quotation-board-group.company'),
            self::STATUS      => __('crm::enums/quotation-board-group.status'),
        };
    }

    public function field(): string
    {
        return match ($this) {
            self::SALESPERSON => 'user_id',
            self::SALES_TEAM  => 'team_id',
            self::CUSTOMER    => 'partner_id',
            self::SOURCE      => 'utm_source_id',
            self::MEDIUM      => 'medium_id',
            self::COMPANY     => 'company_id',
            self::STATUS      => 'state',
        };
    }

    public function relation(): ?string
    {
        return match ($this) {
            self::SALESPERSON => 'user',
            self::SALES_TEAM  => 'team',
            self::CUSTOMER    => 'partner',
            self::SOURCE      => 'utmSource',
            self::MEDIUM      => 'medium',
            self::COMPANY     => 'company',
            self::STATUS      => null,
        };
    }

    public function isMovable(): bool
    {
        return match ($this) {
            self::SALESPERSON, self::SALES_TEAM, self::SOURCE, self::MEDIUM => true,
            self::CUSTOMER, self::COMPANY, self::STATUS                     => false,
        };
    }
}
