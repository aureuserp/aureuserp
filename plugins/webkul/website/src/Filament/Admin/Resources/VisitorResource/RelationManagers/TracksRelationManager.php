<?php

namespace Webkul\Website\Filament\Admin\Resources\VisitorResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Website\Filament\Admin\Resources\TrackResource;

class TracksRelationManager extends RelationManager
{
    protected static string $relationship = 'tracks';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('website::filament/admin/resources/track.plural-model-label');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return TrackResource::table($table, withVisitor: false);
    }
}
