<?php

namespace Webkul\Crm\Filament\Support;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Webkul\Crm\Models\Lead;

class SimilarLeads
{
    protected static array $counts = [];

    public static function count(Lead $record): int
    {
        return static::$counts[$record->getKey()] ??= static::visibleRecords($record)->count();
    }

    public static function visibleRecords(Lead $record): Collection
    {
        return $record->similarRecordsQuery()
            ->with(['stage', 'user', 'partner', 'tags', 'company.currency'])
            ->get()
            ->filter(fn (Lead $lead): bool => LeadResourceResolver::resourceFor($lead)::canView($lead))
            ->values();
    }

    public static function forgetCount(Lead $record): void
    {
        unset(static::$counts[$record->getKey()]);
    }

    public static function navigationItems(Page $page, string $similarPage): array
    {
        $record = method_exists($page, 'getRecord') ? $page->getRecord() : null;

        if (! $record instanceof Lead || ! ($count = static::count($record))) {
            return [];
        }

        return [
            NavigationItem::make('similar-leads')
                ->label(__('crm::filament/similar-leads.navigation.label'))
                ->icon('heroicon-o-user-group')
                ->badge((string) $count)
                ->url($page::getResource()::getUrl('similar', ['record' => $record]))
                ->isActiveWhen(fn (): bool => $page instanceof $similarPage),
        ];
    }
}
