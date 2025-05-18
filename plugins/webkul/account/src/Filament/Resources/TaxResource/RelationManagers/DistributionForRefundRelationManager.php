<?php

namespace Webkul\Account\Filament\Resources\TaxResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Webkul\Account\Enums;
use Webkul\Account\Traits\TaxPartition;

class DistributionForRefundRelationManager extends RelationManager
{
    use TaxPartition;

    protected static string $relationship = 'distributionForRefund';

    protected static ?string $title = 'Distribution for Refund';

    public function getDocumentType(): string
    {
        return Enums\DocumentType::INVOICE->value;
    }
}
