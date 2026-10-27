<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContactBoardGroup: string implements HasLabel
{
    case SALESPERSON = 'user.name';

    case COMPANY = 'parent.name';

    case COUNTRY = 'country.name';

    case INDUSTRY = 'industry.name';

    case TITLE = 'title.name';

    case ACCOUNT_TYPE = 'account_type';

    case JOB_POSITION = 'job_title';

    public static function fromGrouping(?string $grouping): ?self
    {
        return self::tryFrom((string) str((string) $grouping)->before(':'));
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::SALESPERSON  => __('crm::enums/contact-board-group.salesperson'),
            self::COMPANY      => __('crm::enums/contact-board-group.company'),
            self::COUNTRY      => __('crm::enums/contact-board-group.country'),
            self::INDUSTRY     => __('crm::enums/contact-board-group.industry'),
            self::TITLE        => __('crm::enums/contact-board-group.title'),
            self::ACCOUNT_TYPE => __('crm::enums/contact-board-group.account-type'),
            self::JOB_POSITION => __('crm::enums/contact-board-group.job-position'),
        };
    }

    public function field(): string
    {
        return match ($this) {
            self::SALESPERSON  => 'user_id',
            self::COMPANY      => 'parent_id',
            self::COUNTRY      => 'country_id',
            self::INDUSTRY     => 'industry_id',
            self::TITLE        => 'title_id',
            self::ACCOUNT_TYPE => 'account_type',
            self::JOB_POSITION => 'job_title',
        };
    }

    public function relation(): ?string
    {
        return match ($this) {
            self::SALESPERSON  => 'user',
            self::COMPANY      => 'parent',
            self::COUNTRY      => 'country',
            self::INDUSTRY     => 'industry',
            self::TITLE        => 'title',
            self::ACCOUNT_TYPE => null,
            self::JOB_POSITION => null,
        };
    }

    public function isMovable(): bool
    {
        return match ($this) {
            self::SALESPERSON, self::COUNTRY, self::INDUSTRY, self::TITLE => true,
            self::COMPANY, self::ACCOUNT_TYPE, self::JOB_POSITION         => false,
        };
    }
}
