<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\CustomerBoardGroup;
use Webkul\Crm\Filament\Actions\DownloadCustomersVCardBulkAction;
use Webkul\Crm\Filament\Actions\GrantCustomersPortalAccessBulkAction;
use Webkul\Crm\Filament\Actions\MergeCustomersBulkAction;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Actions\SendCustomersEmailBulkAction;
use Webkul\Crm\Filament\Actions\SendCustomersSMSBulkAction;
use Webkul\Crm\Filament\Exports\CustomerExporter;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Partner\Filament\Resources\PartnerResource\Tables\PartnersTable;
use Webkul\Partner\Models\Partner;

class CustomersTable
{
    public static function configure(Table $table, bool $grid = false): Table
    {
        $table = $grid
            ? $table
                ->columns(static::gridColumns($table))
                ->contentGrid(['sm' => 1, 'md' => 2, 'xl' => 3, '2xl' => 3])
            : $table
                ->columns(static::listColumns())
                ->contentGrid(null);

        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['parent', 'country', 'user'])
                ->withCount(['crmOpportunities', 'crmSaleOrders']))
            ->groups(array_map(
                fn (CustomerBoardGroup $group): Group => Group::make($group->value)
                    ->label($group->getLabel())
                    ->getTitleFromRecordUsing(fn (Partner $record): string => static::groupTitle($record, $group))
                    ->collapsible(),
                CustomerBoardGroup::cases(),
            ))
            ->toolbarActions([
                RecordBulkActions::make(
                    exporter: CustomerExporter::class,
                    archivable: true,
                    copyRelations: ['tags'],
                    actions: [
                        MergeCustomersBulkAction::make(),
                        SendCustomersEmailBulkAction::make(),
                        SendCustomersSMSBulkAction::make(),
                        DownloadCustomersVCardBulkAction::make(),
                        GrantCustomersPortalAccessBulkAction::make(),
                    ],
                ),
            ]);
    }

    public static function groupTitle(Partner $record, CustomerBoardGroup $group): string
    {
        $value = $group->relation() ? $record->{$group->relation()}?->name : $record->{$group->field()};

        return match (true) {
            $value instanceof \BackedEnum && method_exists($value, 'getLabel') => $value->getLabel(),
            filled($value)                                                     => (string) $value,
            default                                                            => Dimension::getNoneLabel(),
        };
    }

    public static function listColumns(): array
    {
        return [
            ImageColumn::make('avatar')
                ->label('')
                ->circular()
                ->imageSize(32)
                ->grow(false),
            TextColumn::make('name')
                ->label(__('crm::filament/clusters/sales/resources/customer.table.columns.name'))
                ->weight('medium')
                ->searchable()
                ->sortable(),
            TextColumn::make('phone')
                ->label(__('crm::filament/clusters/sales/resources/customer.table.columns.phone'))
                ->searchable(['phone', 'mobile'])
                ->placeholder('—'),
            TextColumn::make('email')
                ->label(__('crm::filament/clusters/sales/resources/customer.table.columns.email'))
                ->searchable()
                ->sortable()
                ->placeholder('—'),
            TextColumn::make('user.name')
                ->label(__('crm::filament/clusters/sales/resources/customer.table.columns.salesperson'))
                ->badge()
                ->color('gray')
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('city')
                ->label(__('crm::filament/clusters/sales/resources/customer.table.columns.city'))
                ->searchable()
                ->sortable()
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('country.name')
                ->label(__('crm::filament/clusters/sales/resources/customer.table.columns.country'))
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('parent.name')
                ->label(__('crm::filament/clusters/sales/resources/customer.table.columns.company'))
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function gridColumns(Table $table): array
    {
        return PartnersTable::configure(Table::make($table->getLivewire()))->getColumnsLayout();
    }
}
