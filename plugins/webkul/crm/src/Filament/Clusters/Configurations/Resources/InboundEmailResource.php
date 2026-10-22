<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\InboundEmailResource\Pages\ManageInboundEmails;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\InboundEmailResource\Tables\InboundEmailsTable;
use Webkul\Crm\Models\InboundEmail;

class InboundEmailResource extends Resource
{
    protected static ?string $model = InboundEmail::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/inbound-email.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/inbound-email.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/inbound-email.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/mail-server.navigation.group');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return InboundEmailsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInboundEmails::route('/'),
        ];
    }
}
