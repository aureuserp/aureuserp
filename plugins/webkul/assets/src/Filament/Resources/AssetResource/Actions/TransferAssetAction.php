<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Actions;

use Closure;
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

class TransferAssetAction
{
    use HasCustodyHolderFields;

    public static function make(?string $name = 'transfer'): Action
    {
        $sameHolderRule = fn (Get $get, Asset $record) => function (string $attribute, $value, Closure $fail) use ($get, $record): void {
            $targetMode = $get('allocation_mode') ?? 'employee_custody';
            $targetEmployeeId = $targetMode === 'site_asset' ? 0 : (int) ($get('employee_id') ?? 0);
            $targetSiteId = (int) ($get('site_id') ?? 0);
            $currentEmployeeId = (int) ($record->current_employee_id ?? 0);
            $currentSiteId = (int) ($record->current_site_id ?? 0);

            if ($targetEmployeeId === $currentEmployeeId && $targetSiteId === $currentSiteId) {
                $fail(__('assets::services/asset-custody.same-holder'));
            }
        };

        $holderFields = static::getHolderFields('transfer', [$sameHolderRule]);

        return Action::make($name)
            ->label(__('assets::filament/resources/asset.actions.transfer.label'))
            ->icon('heroicon-o-arrows-right-left')
            ->color('warning')
            ->visible(fn (Asset $record): bool => ! $record->trashed()
                && $record->status === AssetStatus::Held
                && (bool) auth()->user()?->can('transfer', $record))
            ->authorize(fn (Asset $record): bool => (bool) auth()->user()?->can('transfer', $record))
            ->modalHeading(__('assets::filament/resources/asset.actions.transfer.modal.heading'))
            ->modalDescription(__('assets::filament/resources/asset.actions.transfer.modal.description'))
            ->modalSubmitActionLabel(__('assets::filament/resources/asset.actions.transfer.modal.submit'))
            ->schema([
                ...$holderFields,

                Select::make('condition_in')
                    ->label(__('assets::filament/resources/asset.actions.transfer.fields.condition-in'))
                    ->options(AssetCondition::class)
                    ->default(fn (Asset $record): mixed => $record->condition)
                    ->required()
                    ->live(),

                DateTimePicker::make('issued_at')
                    ->label(__('assets::filament/resources/asset.actions.transfer.fields.issued-at'))
                    ->default(now())
                    ->required(),

                DatePicker::make('expected_return_at')
                    ->label(__('assets::filament/resources/asset.actions.transfer.fields.expected-return-at'))
                    ->afterOrEqual('issued_at')
                    ->native(false)
                    ->default(fn (): ?string => filled(app(AssetSettings::class)->default_custody_duration_days) && (int) app(AssetSettings::class)->default_custody_duration_days > 0
                        ? now()->addDays((int) app(AssetSettings::class)->default_custody_duration_days)->toDateString()
                        : null)
                    ->required(fn (Get $get): bool => (bool) app(AssetSettings::class)->require_expected_return_date && ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody')
                    ->visible(fn (Get $get): bool => ($get('allocation_mode') ?? 'employee_custody') === 'employee_custody'),

                Textarea::make('issue_note')
                    ->label(__('assets::filament/resources/asset.actions.transfer.fields.issue-note'))
                    ->rows(3),

                Textarea::make('return_note')
                    ->label(__('assets::filament/resources/asset.actions.transfer.fields.return-note'))
                    ->rows(3),

                static::attachmentUploadField('return_attachments', __('assets::filament/resources/asset.actions.transfer.fields.return-attachments'))
                    ->required(function (Get $get): bool {
                        $settings = app(AssetSettings::class);
                        $cond = $get('condition_in');
                        $val = $cond instanceof AssetCondition ? $cond->value : (string) $cond;

                        return (bool) $settings->require_attachments_on_damage
                            && in_array($val, [AssetCondition::Poor->value, AssetCondition::Damaged->value], true);
                    }),

                static::attachmentUploadField('issue_attachments', __('assets::filament/resources/asset.actions.transfer.fields.issue-attachments'))
                    ->required(fn (): bool => (bool) app(AssetSettings::class)->require_attachments_on_custody),
            ])
            ->action(function (Asset $record, array $data, Action $action): void {
                try {
                    app(AssetCustodyService::class)->transfer($record, $data);
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
                    ->title(__('assets::filament/resources/asset.actions.transfer.notification.title'))
                    ->body(__('assets::filament/resources/asset.actions.transfer.notification.body'))
                    ->send();
            });
    }
}
