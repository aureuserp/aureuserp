<?php

namespace Webkul\Crm\Filament\Support;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Set;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Services\LeadScoring;

class ProbabilityHint
{
    public static function detailsAction(): Action
    {
        return Action::make('probabilityDetails')
            ->label(__('crm::filament/lead-scoring.popover.heading'))
            ->tooltip(__('crm::filament/lead-scoring.popover.heading'))
            ->icon('heroicon-o-information-circle')
            ->iconButton()
            ->color('gray')
            ->visible(fn (mixed $record): bool => $record instanceof Lead && $record->exists)
            ->modalHeading(__('crm::filament/lead-scoring.popover.heading'))
            ->modalWidth('md')
            ->modalSubmitAction(false)
            ->schema(function (Lead $record): array {
                $explanation = app(LeadScoring::class)->explain($record);
                $probability = $explanation['probability'];

                return [
                    TextEntry::make('computed_probability')
                        ->label(__('crm::filament/lead-scoring.popover.computed'))
                        ->state($probability === null ? __('crm::filament/lead-scoring.popover.no-history') : number_format((float) $probability, 2).'%'),
                    TextEntry::make('scoring_team')
                        ->label(__('crm::filament/lead-scoring.popover.team'))
                        ->state($explanation['team'] ?? __('crm::filament/lead-scoring.popover.no-team')),
                    TextEntry::make('manual_probability')
                        ->hiddenLabel()
                        ->state(__('crm::filament/lead-scoring.popover.manual'))
                        ->color('gray')
                        ->visible($probability !== null && ! $record->is_automated_probability),
                    TextEntry::make('positive_factors')
                        ->label(__('crm::filament/lead-scoring.popover.positives'))
                        ->state($explanation['positives'])
                        ->bulleted()
                        ->color('success')
                        ->visible(filled($explanation['positives'])),
                    TextEntry::make('negative_factors')
                        ->label(__('crm::filament/lead-scoring.popover.negatives'))
                        ->state($explanation['negatives'])
                        ->bulleted()
                        ->color('danger')
                        ->visible(filled($explanation['negatives'])),
                ];
            });
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
