<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\Concerns\HasCustodyHolderFields;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Asset\Settings\AssetSettings;

class TerminateAssetAction
{
    use HasCustodyHolderFields;

    public static function make(string $name, AssetStatus $targetStatus): Action
    {
        $isScrap = $targetStatus === AssetStatus::Scrapped;

        $translationKey = $isScrap ? 'scrap' : 'mark-lost';
        $icon = $isScrap ? 'heroicon-o-trash' : 'heroicon-o-question-mark-circle';

        return Action::make($name)
            ->label(__("assets::filament/resources/asset.actions.{$translationKey}.label"))
            ->icon($icon)
            ->color('danger')
            ->visible(fn (Asset $record): bool => ! $record->trashed()
                && in_array($record->status, [AssetStatus::Available, AssetStatus::Held], true)
                && (bool) auth()->user()?->can('terminate', $record))
            ->authorize(fn (Asset $record): bool => (bool) auth()->user()?->can('terminate', $record))
            ->modalHeading(__("assets::filament/resources/asset.actions.{$translationKey}.modal.heading"))
            ->modalDescription(__("assets::filament/resources/asset.actions.{$translationKey}.modal.description"))
            ->modalSubmitActionLabel(__("assets::filament/resources/asset.actions.{$translationKey}.modal.submit"))
            ->schema([
                DateTimePicker::make('ended_at')
                    ->label(__("assets::filament/resources/asset.actions.{$translationKey}.fields.ended-at"))
                    ->default(now())
                    ->required(),

                Textarea::make('note')
                    ->label(__("assets::filament/resources/asset.actions.{$translationKey}.fields.note"))
                    ->rows(3)
                    ->columnSpanFull(),

                static::attachmentUploadField('return_attachments', __("assets::filament/resources/asset.actions.{$translationKey}.fields.attachments"))
                    ->required(fn (): bool => (bool) app(AssetSettings::class)->require_attachments_on_termination || ($isScrap && (bool) app(AssetSettings::class)->require_attachments_on_damage)),
            ])
            ->action(function (Asset $record, array $data, Action $action) use ($targetStatus, $translationKey): void {
                $payload = [
                    'ended_at'           => $data['ended_at'] ?? now(),
                    'return_note'        => $data['note'] ?? null,
                    'return_attachments' => $data['return_attachments'] ?? null,
                ];

                try {
                    if ($targetStatus === AssetStatus::Scrapped) {
                        app(AssetCustodyService::class)->scrap($record, $payload);
                    } else {
                        app(AssetCustodyService::class)->markLost($record, $payload);
                    }
                } catch (ValidationException $e) {
                    Notification::make()
                        ->danger()
                        ->title(collect($e->errors())->flatten()->first())
                        ->send();

                    $action->halt();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(__("assets::filament/resources/asset.actions.{$translationKey}.notification.title"))
                    ->body(__("assets::filament/resources/asset.actions.{$translationKey}.notification.body"))
                    ->send();
            });
    }
}
