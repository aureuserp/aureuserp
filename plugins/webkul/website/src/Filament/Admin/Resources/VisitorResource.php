<?php

namespace Webkul\Website\Filament\Admin\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Website\Filament\Admin\Resources\VisitorResource\Pages\ListVisitors;
use Webkul\Website\Filament\Admin\Resources\VisitorResource\Pages\ViewVisitor;
use Webkul\Website\Filament\Admin\Resources\VisitorResource\RelationManagers\TracksRelationManager;
use Webkul\Website\Models\Visitor;

class VisitorResource extends Resource
{
    protected static ?string $model = Visitor::class;

    protected static ?string $slug = 'website/visitors';

    protected static ?int $navigationSort = 3;

    protected static bool $isGloballySearchable = false;

    public static function getRecordTitle(?Model $record): string
    {
        return $record instanceof Visitor ? $record->display_name : static::getModelLabel();
    }

    public static function getModelLabel(): string
    {
        return __('website::filament/admin/resources/visitor.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('website::filament/admin/resources/visitor.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('website::filament/admin/resources/visitor.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Website;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['partner', 'country', 'lastTrack.page'])->withCount('tracks');
    }

    public static function table(Table $table): Table
    {
        $label = fn (string $key): string => __('website::filament/admin/resources/visitor.'.$key);

        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label($label('fields.name'))
                    ->description(fn (Visitor $record): ?string => $record->partner?->email)
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas('partner', fn (Builder $query): Builder => $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))),
                IconColumn::make('is_connected')
                    ->label($label('fields.connected'))
                    ->state(fn (Visitor $record): bool => $record->isConnected())
                    ->boolean(),
                TextColumn::make('country.name')
                    ->label($label('fields.country'))
                    ->placeholder('—'),
                TextColumn::make('lang')
                    ->label($label('fields.language'))
                    ->formatStateUsing(fn (?string $state): string => static::languageName($state))
                    ->placeholder('—'),
                TextColumn::make('visit_count')
                    ->label($label('fields.visits'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tracks_count')
                    ->label($label('fields.page-views'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_page')
                    ->label($label('fields.last-page'))
                    ->state(fn (Visitor $record): ?string => static::trackLabel($record->lastTrack))
                    ->limit(40)
                    ->placeholder('—'),
                TextColumn::make('last_connection_datetime')
                    ->label($label('fields.last-action'))
                    ->since()
                    ->sortable(),
                TextColumn::make('timezone')
                    ->label($label('fields.timezone'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label($label('fields.first-visit'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('last_connection_datetime', 'desc')
            ->filters([
                TernaryFilter::make('connected')
                    ->label($label('fields.connected'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->where('last_connection_datetime', '>=', now()->subMinutes(Visitor::CONNECTED_MINUTES)),
                        false: fn (Builder $query): Builder => $query->where(fn (Builder $query): Builder => $query->whereNull('last_connection_datetime')->orWhere('last_connection_datetime', '<', now()->subMinutes(Visitor::CONNECTED_MINUTES))),
                    ),
                TernaryFilter::make('partner_id')
                    ->label($label('filters.contact'))
                    ->nullable(),
                SelectFilter::make('country_id')
                    ->label($label('fields.country'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        $label = fn (string $key): string => __('website::filament/admin/resources/visitor.'.$key);

        return $schema
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('display_name')
                            ->label($label('fields.name')),
                        TextEntry::make('partner.email')
                            ->label($label('fields.email'))
                            ->placeholder('—'),
                        IconEntry::make('is_connected')
                            ->label($label('fields.connected'))
                            ->state(fn (Visitor $record): bool => $record->isConnected())
                            ->boolean(),
                        TextEntry::make('country.name')
                            ->label($label('fields.country'))
                            ->placeholder('—'),
                        TextEntry::make('lang')
                            ->label($label('fields.language'))
                            ->formatStateUsing(fn (?string $state): string => static::languageName($state))
                            ->placeholder('—'),
                        TextEntry::make('timezone')
                            ->label($label('fields.timezone'))
                            ->placeholder('—'),
                        TextEntry::make('visit_count')
                            ->label($label('fields.visits')),
                        TextEntry::make('tracks_count')
                            ->label($label('fields.page-views'))
                            ->state(fn (Visitor $record): int => $record->tracks()->count()),
                        TextEntry::make('last_page')
                            ->label($label('fields.last-page'))
                            ->state(fn (Visitor $record): ?string => static::trackLabel($record->lastTrack))
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->label($label('fields.first-visit'))
                            ->dateTime(),
                        TextEntry::make('last_connection_datetime')
                            ->label($label('fields.last-action'))
                            ->dateTime()
                            ->since(),
                    ]),
            ]);
    }

    public static function trackLabel(mixed $track): ?string
    {
        if (! $track) {
            return null;
        }

        return $track->page?->title ?? parse_url($track->url, PHP_URL_PATH) ?? $track->url;
    }

    public static function languageName(?string $code): string
    {
        return (string) (config('app.supported_locales.'.$code.'.label') ?? $code);
    }

    public static function getRelations(): array
    {
        return [
            TracksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisitors::route('/'),
            'view'  => ViewVisitor::route('/{record}'),
        ];
    }
}
