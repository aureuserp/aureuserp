<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource\Pages\ManageMailServers;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource\Schemas\MailServerForm;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource\Tables\MailServersTable;
use Webkul\Crm\Models\MailServer;
use Webkul\Support\Filament\Clusters\Settings;

class MailServerResource extends Resource
{
    protected static ?string $model = MailServer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/mail-server.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/mail-server.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/mail-server.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/mail-server.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return MailServerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MailServersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMailServers::route('/'),
        ];
    }
}
