<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Schemas;

use Filament\Schemas\Schema;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\TimeOffResource as BaseByEmployeeResourceForm;

class ByEmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return BaseByEmployeeResourceForm::form($schema);
    }
}
