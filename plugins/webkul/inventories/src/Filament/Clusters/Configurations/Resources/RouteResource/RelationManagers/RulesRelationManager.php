<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource;

class RulesRelationManager extends RelationManager
{
    protected static string $relationship = 'rules';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('inventories::filament/clusters/configurations/resources/route/relation-managers/rules.title');
    }

    public function form(Schema $schema): Schema
    {
        return RuleResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return RuleResource::table($table)
            ->columns([
                TextColumn::make('action')
                    ->searchable(),
                TextColumn::make('sourceLocation.full_name')
                    ->searchable(),
                TextColumn::make('destinationLocation.full_name')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/configurations/resources/route/relation-managers/rules.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->fillForm(function (array $arguments): array {
                        return [
                            'route_id' => $this->getOwnerRecord()->id,
                        ];
                    })
                    ->mutateDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        $data['company_id'] = $data['company_id'] ?? Auth::user()->default_company_id;

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/route/relation-managers/rules.table.header-actions.create.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/route/relation-managers/rules.table.header-actions.create.notification.body')),
                    ),
            ]);
    }
}
