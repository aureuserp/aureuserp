<?php

namespace Webkul\Product\Filament\Resources;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Models\Packaging;

class PackagingResource extends Resource
{
    protected static ?string $model = Packaging::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('products::filament/resources/packaging.form.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('barcode')
                    ->label(__('products::filament/resources/packaging.form.barcode'))
                    ->maxLength(255),
                Select::make('product_id')
                    ->label(__('products::filament/resources/packaging.form.product'))
                    ->relationship(
                        'product',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->withTrashed()
                            ->where('type', 'goods')
                            ->orderBy('id'),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($label) {
                        return str_contains($label, ' (Deleted)');
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('qty')
                    ->label(__('products::filament/resources/packaging.form.qty'))
                    ->required()
                    ->numeric()
                    ->minValue(0.00)
                    ->maxValue(99999999),
                Select::make('company_id')
                    ->label(__('products::filament/resources/packaging.form.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('products::filament/resources/packaging.table.columns.name'))
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label(__('products::filament/resources/packaging.table.columns.product'))
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('qty')
                    ->label(__('products::filament/resources/packaging.table.columns.qty'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('barcode')
                    ->label(__('products::filament/resources/packaging.table.columns.barcode'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('products::filament/resources/packaging.table.columns.company'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('products::filament/resources/packaging.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('products::filament/resources/packaging.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('product.name')
                    ->label(__('products::filament/resources/packaging.table.groups.product'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('products::filament/resources/packaging.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('products::filament/resources/packaging.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('product')
                    ->label(__('products::filament/resources/packaging.table.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/packaging.table.actions.edit.notification.title'))
                            ->body(__('products::filament/resources/packaging.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->action(function (Packaging $record) {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('products::filament/resources/packaging.table.actions.delete.notification.error.title'))
                                ->body(__('products::filament/resources/packaging.table.actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/packaging.table.actions.delete.notification.success.title'))
                            ->body(__('products::filament/resources/packaging.table.actions.delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print')
                        ->label(__('products::filament/resources/packaging.table.bulk-actions.print.label'))
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $pdf = PDF::loadView('products::filament.resources.packagings.actions.print', [
                                'records' => $records,
                            ]);

                            $pdf->setPaper('a4', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Packaging-Barcode.pdf');
                        }),
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('products::filament/resources/packaging.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('products::filament/resources/packaging.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/packaging.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('products::filament/resources/packaging.table.bulk-actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label(__('products::filament/resources/packaging.table.empty-state-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/packaging.table.empty-state-actions.create.notification.title'))
                            ->body(__('products::filament/resources/packaging.table.empty-state-actions.create.notification.body')),
                    ),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('products::filament/resources/packaging.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('products::filament/resources/packaging.infolist.sections.general.entries.name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->columnSpan(2)
                            ->icon('heroicon-o-gift'),

                        TextEntry::make('barcode')
                            ->label(__('products::filament/resources/packaging.infolist.sections.general.entries.barcode'))
                            ->icon('heroicon-o-bars-4')
                            ->placeholder('—'),

                        TextEntry::make('product.name')
                            ->label(__('products::filament/resources/packaging.infolist.sections.general.entries.product'))
                            ->icon('heroicon-o-cube')
                            ->placeholder('—'),

                        TextEntry::make('qty')
                            ->label(__('products::filament/resources/packaging.infolist.sections.general.entries.qty'))
                            ->icon('heroicon-o-scale')
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make(__('products::filament/resources/packaging.infolist.sections.organization.title'))
                    ->schema([
                        TextEntry::make('company.name')
                            ->label(__('products::filament/resources/packaging.infolist.sections.organization.entries.company'))
                            ->icon('heroicon-o-building-office')
                            ->placeholder('—'),

                        TextEntry::make('creator.name')
                            ->label(__('products::filament/resources/packaging.infolist.sections.organization.entries.creator'))
                            ->icon('heroicon-o-user')
                            ->placeholder('—'),

                        TextEntry::make('created_at')
                            ->label(__('products::filament/resources/packaging.infolist.sections.organization.entries.created_at'))
                            ->dateTime()
                            ->icon('heroicon-o-calendar')
                            ->placeholder('—'),

                        TextEntry::make('updated_at')
                            ->label(__('products::filament/resources/packaging.infolist.sections.organization.entries.updated_at'))
                            ->dateTime()
                            ->icon('heroicon-o-clock')
                            ->placeholder('—'),
                    ])
                    ->collapsible()
                    ->columns(2),
            ]);
    }
}
