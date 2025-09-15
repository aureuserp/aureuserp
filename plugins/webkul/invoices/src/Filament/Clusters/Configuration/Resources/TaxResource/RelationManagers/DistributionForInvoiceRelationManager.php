<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Traits\TaxPartition;

class DistributionForInvoiceRelationManager extends RelationManager
{
    use TaxPartition;

    protected static string $relationship = 'distributionForInvoice';

    protected static ?string $title = 'Distribution for Invoice';

    public function getDocumentType(): string
    {
        return DocumentType::INVOICE->value;
    }
}
