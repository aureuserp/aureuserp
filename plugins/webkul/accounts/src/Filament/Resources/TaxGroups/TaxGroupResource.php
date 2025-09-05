<?php

namespace Webkul\Account\Filament\Resources\TaxGroups;

use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\TaxGroups\Pages\CreateTaxGroup;
use Webkul\Account\Filament\Resources\TaxGroups\Pages\EditTaxGroup;
use Webkul\Account\Filament\Resources\TaxGroups\Pages\ListTaxGroups;
use Webkul\Account\Filament\Resources\TaxGroups\Pages\ViewTaxGroup;
use Webkul\Account\Filament\Resources\TaxGroups\Schemas\TaxGroupForm;
use Webkul\Account\Filament\Resources\TaxGroups\Tables\TaxGroupsTable;
use Webkul\Account\Models\TaxGroup;

class TaxGroupResource extends Resource
{
    protected static ?string $model = TaxGroup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return TaxGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxGroupsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('company.name')
                            ->icon('heroicon-o-building-office-2')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.company')),
                        TextEntry::make('country.name')
                            ->icon('heroicon-o-globe-alt')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.country')),
                        TextEntry::make('name')
                            ->icon('heroicon-o-tag')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.name')),
                        TextEntry::make('preceding_subtotal')
                            ->icon('heroicon-o-rectangle-group')
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/tax-group.infolist.sections.entries.preceding-subtotal')),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaxGroups::route('/'),
            'create' => CreateTaxGroup::route('/create'),
            'view'   => ViewTaxGroup::route('/{record}'),
            'edit'   => EditTaxGroup::route('/{record}/edit'),
        ];
    }
}
