<?php

namespace Webkul\Security\Filament\Resources\Companies\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Security\Filament\Resources\Companies\RelationManagers\Schemas\BranchForm;
use Webkul\Security\Filament\Resources\Companies\RelationManagers\Schemas\BranchInfolist;
use Webkul\Security\Filament\Resources\Companies\RelationManagers\Tables\BranchesTable;

class BranchesRelationManager extends RelationManager
{
    protected static string $relationship = 'branches';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return BranchForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return BranchesTable::configure($table);
    }

    public function infolist(Schema $schema): Schema
    {
        return BranchInfolist::configure($schema);
    }
}
