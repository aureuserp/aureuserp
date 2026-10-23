<?php

namespace Webkul\Crm\Filament\Support;

use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Services\LeadScoring;

class ProbabilityHint
{
    public static function component(): View
    {
        return View::make('crm::filament.lead-scoring.popover')
            ->visible(fn (mixed $record): bool => $record instanceof Lead && $record->exists)
            ->viewData(fn (mixed $record): array => $record instanceof Lead && $record->exists ? [
                'explanation' => app(LeadScoring::class)->explain($record),
                'isAutomated' => (bool) $record->is_automated_probability,
            ] : []);
    }

    public static function canReset(mixed $record): bool
    {
        return $record instanceof Lead
            && $record->exists
            && ! $record->is_automated_probability
            && $record->automated_probability !== null
            && ! $record->trashed();
    }

    public static function formResetAction(): Action
    {
        return Action::make('useAutomatedProbability')
            ->label(__('crm::filament/lead-scoring.reset'))
            ->tooltip(__('crm::filament/lead-scoring.reset'))
            ->icon('heroicon-m-arrow-path')
            ->iconButton()
            ->visible(fn (mixed $record): bool => static::canReset($record))
            ->action(fn (Set $set, Lead $record) => $set('probability', (float) $record->automated_probability));
    }

    public static function viewResetAction(): Action
    {
        return Action::make('useAutomatedProbability')
            ->label(__('crm::filament/lead-scoring.reset'))
            ->tooltip(__('crm::filament/lead-scoring.reset'))
            ->icon('heroicon-m-arrow-path')
            ->iconButton()
            ->visible(fn (mixed $record): bool => static::canReset($record))
            ->authorize(fn (Lead $record): bool => LeadResourceResolver::resourceFor($record)::canEdit($record))
            ->action(fn (Lead $record) => $record->update(['is_automated_probability' => true]));
    }
}
