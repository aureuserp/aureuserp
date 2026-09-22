<?php

namespace Webkul\Product\Filament\Resources\PriceListResource\Schemas;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Webkul\Product\Enums\PriceRuleApplyTo;
use Webkul\Product\Enums\PriceRuleBase;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\Product;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;
use Webkul\Support\Models\Currency;

class PriceListForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('products::filament/resources/price-list.form.section.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('products::filament/resources/price-list.form.section.general.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('currency_id')
                            ->label(__('products::filament/resources/price-list.form.section.general.fields.currency'))
                            ->relationship(
                                name: 'currency',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->active(),
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn (): ?int => default_currency_id()),
                        Select::make('company_id')
                            ->label(__('products::filament/resources/price-list.form.section.general.fields.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn (): ?int => current_company_id()),
                        Toggle::make('is_active')
                            ->label(__('products::filament/resources/price-list.form.section.general.fields.status'))
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make(__('products::filament/resources/price-list.form.section.rules.title'))
                    ->description(__('products::filament/resources/price-list.form.section.rules.description'))
                    ->schema([
                        static::getRulesRepeater(),
                    ]),
            ])
            ->columns(1);
    }

    private static function getRulesRepeater(): Repeater
    {
        return Repeater::make('items')
            ->relationship('items')
            ->hiddenLabel()
            ->defaultItems(0)
            ->compact()
            ->addActionLabel(__('products::filament/resources/price-list.form.section.rules.add-rule'))
            ->addAction(function (Action $action): Action {
                return $action
                    ->schema(fn (Schema $schema): Schema => $schema->components(static::getRuleFormSchema()))
                    ->modalWidth(Width::FourExtraLarge)
                    ->fillForm(fn (): array => static::getDefaultRuleData())
                    ->action(function (array $data, Repeater $component): void {
                        $state = $component->getState() ?? [];

                        $state[(string) Str::uuid()] = static::normalizeRuleData($data);

                        $component->state($state);
                    });
            })
            ->table([
                RepeaterTableColumn::make('display_scope')
                    ->label(__('products::filament/resources/price-list.form.section.rules.columns.apply-to'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('display_target')
                    ->label(__('products::filament/resources/price-list.form.section.rules.columns.target'))
                    ->resizable(),
                RepeaterTableColumn::make('display_min_quantity')
                    ->label(__('products::filament/resources/price-list.form.section.rules.columns.min-quantity'))
                    ->resizable(),
                RepeaterTableColumn::make('display_type')
                    ->label(__('products::filament/resources/price-list.form.section.rules.columns.type'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('display_price')
                    ->label(__('products::filament/resources/price-list.form.section.rules.columns.price'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('display_period')
                    ->label(__('products::filament/resources/price-list.form.section.rules.columns.period'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
            ])
            ->schema([
                Hidden::make('display_apply_to'),
                Hidden::make('product_id'),
                Hidden::make('category_id'),
                Hidden::make('min_quantity'),
                Hidden::make('type'),
                Hidden::make('fixed_price'),
                Hidden::make('percent_price'),
                Hidden::make('base'),
                Hidden::make('base_price_list_id'),
                Hidden::make('price_discount'),
                Hidden::make('price_markup'),
                Hidden::make('price_round'),
                Hidden::make('price_surcharge'),
                Hidden::make('price_min_margin'),
                Hidden::make('price_max_margin'),
                Hidden::make('starts_at'),
                Hidden::make('ends_at'),
                TextEntry::make('display_scope')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => static::scopeLabel(
                        static::enumValue($get('display_apply_to')),
                        $get('product_id'),
                        $get('category_id'),
                    )),
                TextEntry::make('display_target')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => static::describeTarget($get)),
                TextEntry::make('display_min_quantity')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => (string) (float) ($get('min_quantity') ?? 0)),
                TextEntry::make('display_type')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => static::ruleType($get('type'))?->getLabel() ?? '—'),
                TextEntry::make('display_price')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => static::describePrice($get)),
                TextEntry::make('display_period')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => static::describePeriod($get)),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => static::normalizeRuleData($data))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => static::normalizeRuleData($data))
            ->extraItemActions([
                Action::make('editRule')
                    ->icon('heroicon-o-pencil-square')
                    ->label(__('products::filament/resources/price-list.form.section.rules.actions.edit'))
                    ->tooltip(__('products::filament/resources/price-list.form.section.rules.actions.edit'))
                    ->extraAttributes(['data-row-click-action' => 'true'])
                    ->schema(fn (Schema $schema): Schema => $schema->components(static::getRuleFormSchema()))
                    ->modalWidth(Width::FourExtraLarge)
                    ->fillForm(function (array $arguments, Repeater $component): array {
                        return array_replace(
                            static::getDefaultRuleData(),
                            static::toFormData($component->getRawItemState($arguments['item'])),
                        );
                    })
                    ->action(function (array $arguments, array $data, Repeater $component): void {
                        $state = $component->getState() ?? [];

                        $state[$arguments['item']] = static::normalizeRuleData($data);

                        $component->state($state);
                    }),
            ]);
    }

    /**
     * @return array<int, mixed>
     */
    private static function getRuleFormSchema(): array
    {
        $lang = 'products::filament/resources/price-list.form.section.rules.fields.';

        return [
            Grid::make(2)
                ->schema([
                    Group::make()
                        ->schema([
                            Radio::make('display_apply_to')
                                ->label(__($lang.'apply-to'))
                                ->options([
                                    PriceRuleApplyTo::PRODUCT->value  => __($lang.'apply-to-product'),
                                    PriceRuleApplyTo::CATEGORY->value => __($lang.'apply-to-category'),
                                ])
                                ->default(PriceRuleApplyTo::PRODUCT->value)
                                ->inline()
                                ->required()
                                ->live(),
                            Select::make('category_id')
                                ->label(__($lang.'category'))
                                ->placeholder(__($lang.'all-categories'))
                                ->options(fn (): array => Category::query()->orderBy('full_name')->limit(50)->pluck('full_name', 'id')->all())
                                ->getSearchResultsUsing(fn (string $search): array => Category::query()
                                    ->where('full_name', 'like', "%{$search}%")
                                    ->orderBy('full_name')
                                    ->limit(50)
                                    ->pluck('full_name', 'id')
                                    ->all())
                                ->getOptionLabelUsing(fn ($value): ?string => Category::find($value)?->full_name)
                                ->searchable()
                                ->visible(fn (Get $get): bool => static::enumValue($get('display_apply_to')) === PriceRuleApplyTo::CATEGORY->value),
                            Select::make('product_id')
                                ->label(__($lang.'product'))
                                ->placeholder(__($lang.'all-products'))
                                ->options(fn (): array => static::templateOptions())
                                ->getSearchResultsUsing(fn (string $search): array => static::templateOptions($search))
                                ->getOptionLabelUsing(fn ($value): ?string => Product::find($value)?->name)
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('variant_id', null))
                                ->visible(fn (Get $get): bool => static::enumValue($get('display_apply_to')) !== PriceRuleApplyTo::CATEGORY->value),
                            Select::make('variant_id')
                                ->label(__($lang.'variant'))
                                ->placeholder(__($lang.'all-variants'))
                                ->options(fn (Get $get): array => static::variantOptions($get('product_id')))
                                ->getOptionLabelUsing(fn ($value): ?string => Product::find($value)?->name)
                                ->searchable()
                                ->visible(fn (Get $get): bool => static::enumValue($get('display_apply_to')) !== PriceRuleApplyTo::CATEGORY->value
                                    && static::variantOptions($get('product_id')) !== []),
                            Radio::make('type')
                                ->label(__($lang.'type'))
                                ->options(PriceRuleType::class)
                                ->default(PriceRuleType::FIXED->value)
                                ->inline()
                                ->required()
                                ->live(),
                            TextInput::make('fixed_price')
                                ->label(__($lang.'fixed-price'))
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->visible(fn (Get $get): bool => static::enumValue($get('type')) === PriceRuleType::FIXED->value),
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('percent_price')
                                        ->label(__($lang.'percent-price'))
                                        ->helperText(__($lang.'percent-price-helper'))
                                        ->numeric()
                                        ->suffix('%')
                                        ->default(0)
                                        ->required(),
                                    Select::make('base_price_list_id')
                                        ->label(__($lang.'on'))
                                        ->placeholder(__($lang.'sales-price'))
                                        ->options(fn ($livewire): array => static::basePriceListOptions($livewire))
                                        ->getOptionLabelUsing(fn ($value): ?string => PriceList::find($value)?->name)
                                        ->searchable(),
                                ])
                                ->visible(fn (Get $get): bool => static::enumValue($get('type')) === PriceRuleType::PERCENTAGE->value),
                        ]),
                    Group::make()
                        ->schema([
                            TextInput::make('min_quantity')
                                ->label(__($lang.'min-quantity'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            DateTimePicker::make('starts_at')
                                ->label(__($lang.'starts-at'))
                                ->native(false),
                            DateTimePicker::make('ends_at')
                                ->label(__($lang.'ends-at'))
                                ->native(false)
                                ->after('starts_at'),
                        ]),
                ]),
            Grid::make(2)
                ->schema([
                    Group::make()
                        ->schema([
                            Select::make('base')
                                ->label(__($lang.'base'))
                                ->options(PriceRuleBase::class)
                                ->default(PriceRuleBase::LIST_PRICE->value)
                                ->selectablePlaceholder(false)
                                ->required()
                                ->live(),
                            Select::make('base_price_list_id')
                                ->label(__($lang.'base-price-list'))
                                ->options(fn ($livewire): array => static::basePriceListOptions($livewire))
                                ->getOptionLabelUsing(fn ($value): ?string => PriceList::find($value)?->name)
                                ->searchable()
                                ->required()
                                ->live()
                                ->visible(fn (Get $get): bool => static::enumValue($get('base')) === PriceRuleBase::PRICE_RULES->value),
                            TextInput::make('price_discount')
                                ->label(__($lang.'price-discount'))
                                ->numeric()
                                ->suffix('%')
                                ->default(0)
                                ->live(onBlur: true)
                                ->visible(fn (Get $get): bool => static::enumValue($get('base')) !== PriceRuleBase::STANDARD_PRICE->value),
                            TextInput::make('price_markup')
                                ->label(__($lang.'price-markup'))
                                ->numeric()
                                ->suffix('%')
                                ->default(0)
                                ->live(onBlur: true)
                                ->visible(fn (Get $get): bool => static::enumValue($get('base')) === PriceRuleBase::STANDARD_PRICE->value),
                            TextInput::make('price_round')
                                ->label(__($lang.'price-round'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->live(onBlur: true),
                            TextInput::make('price_surcharge')
                                ->label(__($lang.'price-surcharge'))
                                ->numeric()
                                ->default(0)
                                ->live(onBlur: true),
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('price_min_margin')
                                        ->label(__($lang.'price-min-margin'))
                                        ->numeric()
                                        ->default(0),
                                    TextInput::make('price_max_margin')
                                        ->label(__($lang.'price-max-margin'))
                                        ->numeric()
                                        ->default(0),
                                ]),
                        ]),
                    Group::make()
                        ->schema([
                            Placeholder::make('rule_tip')
                                ->hiddenLabel()
                                ->content(fn (Get $get, $livewire): string => static::describeRuleTip($get, $livewire)),
                            Placeholder::make('rounding_tip')
                                ->hiddenLabel()
                                ->content(__('products::filament/resources/price-list.form.section.rules.formula.rounding-tip')),
                        ]),
                ])
                ->visible(fn (Get $get): bool => static::enumValue($get('type')) === PriceRuleType::FORMULA->value),
        ];
    }

    /**
     * Mirrors Odoo's rule tip: restates the formula in words and works a
     * worked example through it.
     */
    private static function describeRuleTip(Get $get, $livewire): string
    {
        $base = static::ruleBase($get('base')) ?? PriceRuleBase::LIST_PRICE;

        $isCostBased = $base === PriceRuleBase::STANDARD_PRICE;

        $shown = (float) ($isCostBased ? $get('price_markup') : $get('price_discount'));

        $discountFactor = (100 - ($isCostBased ? -$shown : $shown)) / 100;

        $discounted = 100 * $discountFactor;

        $round = (float) ($get('price_round') ?? 0);

        if ($round) {
            $discounted = round($discounted / $round) * $round;
        }

        $surcharge = (float) ($get('price_surcharge') ?? 0);

        $currency = static::modalCurrencyCode($livewire);

        return __('products::filament/resources/price-list.form.section.rules.formula.rule-tip', [
            'base'      => $base->getLabel(),
            'discount'  => static::trimNumber($shown),
            'type'      => $isCostBased
                ? __('products::filament/resources/price-list.form.section.rules.formula.markup')
                : __('products::filament/resources/price-list.form.section.rules.formula.discount'),
            'surcharge' => money($surcharge, $currency),
            'amount'    => money(100, $currency),
            'factor'    => static::trimNumber($discountFactor),
            'total'     => money($discounted + $surcharge, $currency),
        ]);
    }

    private static function modalCurrencyCode($livewire): ?string
    {
        $currencyId = null;

        if (is_object($livewire) && property_exists($livewire, 'data') && is_array($livewire->data)) {
            $currencyId = $livewire->data['currency_id'] ?? null;
        }

        if (blank($currencyId) && is_object($livewire) && method_exists($livewire, 'getRecord')) {
            $currencyId = $livewire->getRecord()?->currency_id;
        }

        return Currency::find($currencyId ?? default_currency_id())?->code;
    }

    /**
     * @return array<int, string>
     */
    private static function templateOptions(?string $search = null): array
    {
        return Product::query()
            ->whereNull('parent_id')
            ->when(filled($search), fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->limit(50)
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private static function variantOptions($productId): array
    {
        if (blank($productId)) {
            return [];
        }

        return Product::query()
            ->where('parent_id', $productId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private static function basePriceListOptions($livewire): array
    {
        $currentId = is_object($livewire) && method_exists($livewire, 'getRecord')
            ? $livewire->getRecord()?->getKey()
            : null;

        return PriceList::query()
            ->active()
            ->when($currentId, fn (Builder $query, $priceListId) => $query->whereKeyNot($priceListId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    private static function describeTarget(Get $get): string
    {
        $lang = 'products::filament/resources/price-list.form.section.rules.fields.';

        if (static::enumValue($get('display_apply_to')) === PriceRuleApplyTo::CATEGORY->value) {
            return Category::find($get('category_id'))?->full_name ?? __($lang.'all-categories');
        }

        return Product::withTrashed()->find($get('product_id'))?->name ?? __($lang.'all-products');
    }

    /**
     * The scope a rule ends up with, mirroring what the model stores on save.
     */
    private static function scopeLabel(?string $displayApplyTo, $productId, $categoryId): string
    {
        if ($displayApplyTo === PriceRuleApplyTo::CATEGORY->value) {
            return $categoryId
                ? PriceRuleApplyTo::CATEGORY->getLabel()
                : PriceRuleApplyTo::GLOBAL->getLabel();
        }

        if (blank($productId)) {
            return PriceRuleApplyTo::GLOBAL->getLabel();
        }

        return Product::withTrashed()->whereKey($productId)->value('parent_id')
            ? PriceRuleApplyTo::VARIANT->getLabel()
            : PriceRuleApplyTo::PRODUCT->getLabel();
    }

    private static function describePrice(Get $get): string
    {
        $currencyCode = Currency::find($get('../../currency_id'))?->code;

        return match (static::ruleType($get('type'))) {
            PriceRuleType::FIXED      => money((float) ($get('fixed_price') ?? 0), $currencyCode),
            PriceRuleType::PERCENTAGE => static::percentage($get('percent_price')),
            PriceRuleType::FORMULA    => static::describeFormula($get),
            default                   => '—',
        };
    }

    private static function describeFormula(Get $get): string
    {
        $isCostBased = static::enumValue($get('base')) === PriceRuleBase::STANDARD_PRICE->value;

        $percent = $isCostBased
            ? static::percentage($get('price_markup'))
            : static::percentage($get('price_discount'));

        $base = static::ruleBase($get('base'))?->getLabel();

        return trim($percent.' · '.($base ?? ''), ' ·');
    }

    private static function trimNumber($value): string
    {
        return rtrim(rtrim(number_format((float) ($value ?? 0), 2, '.', ''), '0'), '.');
    }

    private static function percentage($value): string
    {
        return rtrim(rtrim(number_format((float) ($value ?? 0), 2, '.', ''), '0'), '.').'%';
    }

    private static function describePeriod(Get $get): string
    {
        $starts = $get('starts_at');

        $ends = $get('ends_at');

        if (blank($starts) && blank($ends)) {
            return '—';
        }

        return trim((string) $starts.' → '.(string) $ends, ' →');
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    private static function getDefaultRuleData(array $overrides = []): array
    {
        return static::normalizeRuleData([
            'display_apply_to'   => PriceRuleApplyTo::PRODUCT->value,
            'product_id'         => null,
            'variant_id'         => null,
            'category_id'        => null,
            'min_quantity'       => 0,
            'type'               => PriceRuleType::FIXED->value,
            'fixed_price'        => 0,
            'percent_price'      => 0,
            'base'               => PriceRuleBase::LIST_PRICE->value,
            'base_price_list_id' => null,
            'price_discount'     => 0,
            'price_markup'       => 0,
            'price_round'        => 0,
            'price_surcharge'    => 0,
            'price_min_margin'   => 0,
            'price_max_margin'   => 0,
            'starts_at'          => null,
            'ends_at'            => null,
            ...$overrides,
        ]);
    }

    /**
     * Split a stored rule back into the fields the modal edits: the scope radio,
     * the product it was built from and, when the rule targets one, its variant.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function toFormData(array $data): array
    {
        $data['type'] = static::enumValue($data['type'] ?? null) ?? PriceRuleType::FIXED->value;
        $data['base'] = static::enumValue($data['base'] ?? null) ?? PriceRuleBase::LIST_PRICE->value;

        $applyTo = static::enumValue($data['apply_to'] ?? null);

        $data['display_apply_to'] = $applyTo === PriceRuleApplyTo::CATEGORY->value
            ? PriceRuleApplyTo::CATEGORY->value
            : PriceRuleApplyTo::PRODUCT->value;

        $data['variant_id'] = null;

        if ($applyTo === PriceRuleApplyTo::VARIANT->value && filled($data['product_id'] ?? null)) {
            $variant = Product::withTrashed()->find($data['product_id']);

            if ($variant?->parent_id) {
                $data['variant_id'] = $variant->id;
                $data['product_id'] = $variant->parent_id;
            }
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private static function normalizeRuleData(array $data): array
    {
        $data['type'] = static::enumValue($data['type'] ?? null) ?? PriceRuleType::FIXED->value;
        $data['base'] = static::enumValue($data['base'] ?? null) ?? PriceRuleBase::LIST_PRICE->value;

        $data['display_apply_to'] = static::enumValue($data['display_apply_to'] ?? null) === PriceRuleApplyTo::CATEGORY->value
            ? PriceRuleApplyTo::CATEGORY->value
            : PriceRuleApplyTo::PRODUCT->value;

        $variantId = $data['variant_id'] ?? null;

        unset($data['variant_id']);

        if (filled($variantId)) {
            $data['product_id'] = $variantId;
        }

        foreach ([
            'min_quantity',
            'fixed_price',
            'percent_price',
            'price_discount',
            'price_markup',
            'price_round',
            'price_surcharge',
            'price_min_margin',
            'price_max_margin',
        ] as $numeric) {
            $data[$numeric] = (float) ($data[$numeric] ?? 0);
        }

        return $data;
    }

    private static function ruleType($state): ?PriceRuleType
    {
        return PriceRuleType::tryFrom(static::enumValue($state) ?? '');
    }

    private static function ruleBase($state): ?PriceRuleBase
    {
        return PriceRuleBase::tryFrom(static::enumValue($state) ?? '');
    }

    private static function enumValue($state): ?string
    {
        if ($state instanceof BackedEnum) {
            return (string) $state->value;
        }

        return filled($state) ? (string) $state : null;
    }
}
