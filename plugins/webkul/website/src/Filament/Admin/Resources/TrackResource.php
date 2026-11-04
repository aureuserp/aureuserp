<?php

namespace Webkul\Website\Filament\Admin\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Website\Filament\Admin\Resources\TrackResource\Pages\ListTracks;
use Webkul\Website\Models\Track;

class TrackResource extends Resource
{
    protected static ?string $model = Track::class;

    protected static ?string $slug = 'website/page-views';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('website::filament/admin/resources/track.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('website::filament/admin/resources/track.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('website::filament/admin/resources/track.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Website;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table, bool $withVisitor = true): Table
    {
        $label = fn (string $key): string => __('website::filament/admin/resources/track.'.$key);

        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['page', 'visitor.partner']))
            ->columns(array_values(array_filter([
                $withVisitor ? TextColumn::make('visitor.display_name')
                    ->label($label('fields.visitor'))
                    ->url(fn (Track $record): string => VisitorResource::getUrl('view', ['record' => $record->visitor_id])) : null,
                TextColumn::make('page.title')
                    ->label($label('fields.page'))
                    ->placeholder('—'),
                TextColumn::make('url')
                    ->label($label('fields.url'))
                    ->limit(60)
                    ->tooltip(fn (Track $record): string => $record->url)
                    ->searchable(),
                TextColumn::make('visit_datetime')
                    ->label($label('fields.visited-at'))
                    ->dateTime()
                    ->sortable(),
            ])))
            ->defaultSort('visit_datetime', 'desc')
            ->groups($withVisitor ? [
                Group::make('visitor_id')
                    ->label($label('fields.visitor'))
                    ->getTitleFromRecordUsing(fn (Track $record): string => $record->visitor?->display_name ?? '—'),
                Group::make('page.title')
                    ->label($label('fields.page')),
            ] : [])
            ->filters([
                SelectFilter::make('page_id')
                    ->label($label('fields.page'))
                    ->relationship('page', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTracks::route('/'),
        ];
    }
}
