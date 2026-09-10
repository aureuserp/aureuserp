<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource;

class TimeOffForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        $schema = $schema->components((new TimeOffResource)->getFormSchema(true));

        $components = $schema->getComponents();

        $firstSectionChildComponents = $components[0]->getDefaultChildComponents();

        $firstSectionChildComponents[] = Section::make()
            ->visible(! empty($customFormFields))
            ->schema($customFormFields)
            ->columns(2);

        $components[0]->childComponents($firstSectionChildComponents);

        $schema->components($components);

        return $schema;
    }
}
