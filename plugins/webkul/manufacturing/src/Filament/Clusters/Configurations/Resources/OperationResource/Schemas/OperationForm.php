<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Schemas;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Manufacturing\Enums\OperationTimeMode;
use Webkul\Manufacturing\Enums\OperationWorksheetType;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\CreateBillOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages\EditBillOfMaterial;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageBillsOfMaterials;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Product\Models\ProductAttributeValue;

class OperationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpanFull(),

                                Select::make('bill_of_material_id')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.bill-of-material'))
                                    ->relationship('billOfMaterial', 'code')
                                    ->hiddenOn([
                                        CreateBillOfMaterial::class,
                                        EditBillOfMaterial::class,
                                        ManageBillsOfMaterials::class,
                                    ])
                                    ->getOptionLabelFromRecordUsing(fn (BillOfMaterial $record): string => OperationResource::getBillOfMaterialLabel($record))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('attributeValues', []))
                                    ->required(),

                                Select::make('work_center_id')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.work-center'))
                                    ->relationship('workCenter', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm(fn (Schema $schema): Schema => WorkCenterResource::form($schema))
                                    ->createOptionAction(fn (Action $action) => $action->modalWidth(Width::SevenExtraLarge)),

                                Select::make('attributeValues')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.apply-on-variants'))
                                    ->relationship(
                                        'attributeValues',
                                        'id',
                                        modifyQueryUsing: function (Get $get, Builder $query): void {
                                            $billOfMaterial = BillOfMaterial::query()
                                                ->with('product')
                                                ->find($get('bill_of_material_id'));

                                            $productId = $billOfMaterial?->product_id;

                                            if (! $productId) {
                                                $query->whereRaw('1 = 0');

                                                return;
                                            }

                                            $query->where('product_id', $productId);
                                        }
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (ProductAttributeValue $record): string => $record->attributeOption?->name ?? (string) $record->id)
                                    ->searchable()
                                    ->preload()
                                    ->multiple(),

                                Select::make('blockedByOperations')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.blocked-by'))
                                    ->relationship(
                                        'blockedByOperations',
                                        'name',
                                        modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('id', '!=', $get('id'))->where('bill_of_material_id', $get('bill_of_material_id'))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->multiple(),

                                TextEntry::make('company')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.general.fields.company'))
                                    ->state(function (Get $get): string {
                                        $billOfMaterial = BillOfMaterial::query()
                                            ->with('company')
                                            ->find($get('bill_of_material_id'));

                                        return $billOfMaterial?->company?->name ?? '—';
                                    }),
                            ])
                            ->columns(2),

                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.title'))
                            ->schema([
                                Radio::make('worksheet_type')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.worksheet'))
                                    ->options(OperationWorksheetType::class)
                                    ->default(OperationWorksheetType::TEXT->value)
                                    ->inline(false)
                                    ->live()
                                    ->required()
                                    ->columnSpanFull(),

                                FileUpload::make('worksheet')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.pdf'))
                                    ->disk('public')
                                    ->directory('manufacturing/operations/worksheets')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->downloadable()
                                    ->openable()
                                    ->visible(fn (Get $get): bool => static::matchesEnumState($get('worksheet_type'), OperationWorksheetType::PDF))
                                    ->columnSpanFull(),

                                TextInput::make('worksheet_google_slide_url')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.google-slide'))
                                    ->url()
                                    ->placeholder(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.google-slide-placeholder'))
                                    ->visible(fn (Get $get): bool => static::matchesEnumState($get('worksheet_type'), OperationWorksheetType::GOOGLE_SLIDE))
                                    ->columnSpanFull(),

                                Textarea::make('note')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.description'))
                                    ->placeholder(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.worksheet.fields.description-placeholder'))
                                    ->rows(6)
                                    ->visible(fn (Get $get): bool => static::matchesEnumState($get('worksheet_type'), OperationWorksheetType::TEXT))
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.title'))
                            ->schema([
                                Radio::make('time_mode')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.time-mode'))
                                    ->options(OperationTimeMode::class)
                                    ->default(OperationTimeMode::MANUAL->value)
                                    ->inline(false)
                                    ->live()
                                    ->required(),

                                TextInput::make('time_mode_batch')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.time-mode-batch'))
                                    ->numeric()
                                    ->default(10)
                                    ->minValue(1)
                                    ->step('1')
                                    ->prefix(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.time-mode-batch-prefix'))
                                    ->suffix(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.time-mode-batch-suffix'))
                                    ->visible(fn (Get $get): bool => static::matchesEnumState($get('time_mode'), OperationTimeMode::AUTO)),

                                TextInput::make('manual_cycle_time')
                                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.manual-cycle-time'))
                                    ->default('60:00')
                                    ->rule('regex:/^\d+:\d{2}$/')
                                    ->placeholder('60:00')
                                    ->afterStateHydrated(function (TextInput $component, mixed $state): void {
                                        $component->state(format_float_time($state ?? 60, 'minutes'));
                                    })
                                    ->dehydrateStateUsing(fn (?string $state): string => parse_float_time($state, 'minutes'))
                                    ->suffix(__('manufacturing::filament/clusters/configurations/resources/operation.form.sections.settings.fields.manual-cycle-time-suffix')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    private static function matchesEnumState(mixed $state, BackedEnum $enum): bool
    {
        if ($state instanceof BackedEnum) {
            return $state->value === $enum->value;
        }

        return $state === $enum->value;
    }
}
