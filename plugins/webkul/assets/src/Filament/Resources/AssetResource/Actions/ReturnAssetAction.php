<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\Concerns\HasCustodyHolderFields;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Schemas\AssetWarehouseForm;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Asset\Settings\AssetSettings;

class ReturnAssetAction
{
    use HasCustodyHolderFields;

    public static function make(?string $name = 'return'): Action
    {
        return Action::make($name)
            ->label(__('assets::filament/resources/asset.actions.return.label'))
            ->icon('heroicon-o-arrow-left-circle')
            ->color('gray')
            ->visible(fn (Asset $record): bool => ! $record->trashed()
                && $record->status === AssetStatus::Held
                && (bool) auth()->user()?->can('return', $record))
            ->authorize(fn (Asset $record): bool => (bool) auth()->user()?->can('return', $record))
            ->modalHeading(__('assets::filament/resources/asset.actions.return.modal.heading'))
            ->modalDescription(__('assets::filament/resources/asset.actions.return.modal.description'))
            ->modalSubmitActionLabel(__('assets::filament/resources/asset.actions.return.modal.submit'))
            ->schema([
                Select::make('condition_in')
                    ->label(__('assets::filament/resources/asset.actions.return.fields.condition-in'))
                    ->options(AssetCondition::class)
                    ->default(fn (Asset $record): mixed => $record->condition)
                    ->required()
                    ->live(),

                DateTimePicker::make('ended_at')
                    ->label(__('assets::filament/resources/asset.actions.return.fields.ended-at'))
                    ->default(now())
                    ->required(),

                Select::make('warehouse_id')
                    ->label(__('assets::filament/resources/asset.actions.return.fields.warehouse'))
                    ->relationship(
                        'warehouse',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Asset $record) => $query->where('company_id', $record->company_id)->where('is_active', true),
                    )
                    ->default(fn (Asset $record): ?int => Asset::resolveDefaultWarehouseId($record->company_id))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm(fn (Schema $schema): Schema => AssetWarehouseForm::configure($schema))
                    ->createOptionAction(fn (Action $action) => $action
                        ->visible(fn (): bool => AssetWarehouseResource::canCreate())
                        ->authorize(fn (): bool => AssetWarehouseResource::canCreate())),

                Textarea::make('return_note')
                    ->label(__('assets::filament/resources/asset.actions.return.fields.return-note'))
                    ->required(function (Get $get): bool {
                        $cond = $get('condition_in');
                        $val = $cond instanceof AssetCondition ? $cond->value : (string) $cond;

                        return in_array($val, [AssetCondition::Poor->value, AssetCondition::Damaged->value], true);
                    })
                    ->rows(3)
                    ->columnSpanFull(),

                static::attachmentUploadField('return_attachments', __('assets::filament/resources/asset.actions.return.fields.return-attachments'))
                    ->required(function (Get $get): bool {
                        $settings = app(AssetSettings::class);

                        if ($settings->require_attachments_on_custody) {
                            return true;
                        }

                        $cond = $get('condition_in');
                        $val = $cond instanceof AssetCondition ? $cond->value : (string) $cond;

                        return (bool) $settings->require_attachments_on_damage
                            && in_array($val, [AssetCondition::Poor->value, AssetCondition::Damaged->value], true);
                    }),
            ])
            ->action(function (Asset $record, array $data, Action $action): void {
                try {
                    app(AssetCustodyService::class)->return($record, $data);
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
                    ->title(__('assets::filament/resources/asset.actions.return.notification.title'))
                    ->body(__('assets::filament/resources/asset.actions.return.notification.body'))
                    ->send();
            });
    }
}
