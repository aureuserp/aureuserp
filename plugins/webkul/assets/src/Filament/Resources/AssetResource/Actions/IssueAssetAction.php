<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\Concerns\HasCustodyHolderFields;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Asset\Settings\AssetSettings;

class IssueAssetAction
{
    use HasCustodyHolderFields;

    public static function make(?string $name = 'issue'): Action
    {
        return Action::make($name)
            ->label(__('assets::filament/resources/asset.actions.issue.label'))
            ->icon('heroicon-o-arrow-right-circle')
            ->color('success')
            ->visible(fn (Asset $record): bool => ! $record->trashed()
                && $record->status === AssetStatus::Available
                && (bool) auth()->user()?->can('issue', $record))
            ->authorize(fn (Asset $record): bool => (bool) auth()->user()?->can('issue', $record))
            ->modalHeading(__('assets::filament/resources/asset.actions.issue.modal.heading'))
            ->modalDescription(__('assets::filament/resources/asset.actions.issue.modal.description'))
            ->modalSubmitActionLabel(__('assets::filament/resources/asset.actions.issue.modal.submit'))
            ->schema([
                ...static::getHolderFields('issue'),

                Select::make('condition_out')
                    ->label(__('assets::filament/resources/asset.actions.issue.fields.condition-out'))
                    ->options(AssetCondition::class)
                    ->default(fn (Asset $record): mixed => $record->condition)
                    ->required(),

                DateTimePicker::make('issued_at')
                    ->label(__('assets::filament/resources/asset.actions.issue.fields.issued-at'))
                    ->default(now())
                    ->required(),

                DatePicker::make('expected_return_at')
                    ->label(__('assets::filament/resources/asset.actions.issue.fields.expected-return-at'))
                    ->afterOrEqual('issued_at')
                    ->native(false)
                    ->default(fn (): ?string => filled(app(AssetSettings::class)->default_custody_duration_days) && (int) app(AssetSettings::class)->default_custody_duration_days > 0
                        ? now()->addDays((int) app(AssetSettings::class)->default_custody_duration_days)->toDateString()
                        : null)
                    ->required(fn (Get $get): bool => (bool) app(AssetSettings::class)->require_expected_return_date && ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody')
                    ->visible(fn (Get $get): bool => ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody'),

                Textarea::make('issue_note')
                    ->label(__('assets::filament/resources/asset.actions.issue.fields.issue-note'))
                    ->rows(3)
                    ->columnSpanFull(),

                static::attachmentUploadField('issue_attachments', __('assets::filament/resources/asset.actions.issue.fields.issue-attachments'))
                    ->required(fn (): bool => (bool) app(AssetSettings::class)->require_attachments_on_custody),
            ])
            ->action(function (Asset $record, array $data, Action $action): void {
                try {
                    app(AssetCustodyService::class)->issue($record, $data);
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
                    ->title(__('assets::filament/resources/asset.actions.issue.notification.title'))
                    ->body(__('assets::filament/resources/asset.actions.issue.notification.body'))
                    ->send();
            });
    }
}
