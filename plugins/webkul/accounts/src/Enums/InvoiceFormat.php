<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum InvoiceFormat: string implements HasLabel
{
    case FACTURX_X_CII = 'facturx';

    case BIS_BILLING_3 = 'ubl_bis3';

    case XRECHNUNG_CIUS = 'xrechnung';

    case NLCIUS = 'nlcius';

    case BIS_BILLING_3_A_NZ = 'ubl_a_nz';

    case BIS_BILLING_3_SG = 'ubl_sg';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FACTURX_X_CII      => __('accounts::enums/invoice-format.facturx'),
            self::BIS_BILLING_3      => __('accounts::enums/invoice-format.ubl_bis3'),
            self::XRECHNUNG_CIUS     => __('accounts::enums/invoice-format.xrechnung'),
            self::NLCIUS             => __('accounts::enums/invoice-format.nlcius'),
            self::BIS_BILLING_3_A_NZ => __('accounts::enums/invoice-format.ubl_a_nz'),
            self::BIS_BILLING_3_SG   => __('accounts::enums/invoice-format.ubl_sg'),
        };
    }

    public static function options(): array
    {
        return [
            self::FACTURX_X_CII      => __('accounts::enums/invoice-format.facturx'),
            self::BIS_BILLING_3      => __('accounts::enums/invoice-format.ubl_bis3'),
            self::XRECHNUNG_CIUS     => __('accounts::enums/invoice-format.xrechnung'),
            self::NLCIUS             => __('accounts::enums/invoice-format.nlcius'),
            self::BIS_BILLING_3_A_NZ => __('accounts::enums/invoice-format.ubl_a_nz'),
            self::BIS_BILLING_3_SG   => __('accounts::enums/invoice-format.ubl_sg'),
        ];
    }
}
