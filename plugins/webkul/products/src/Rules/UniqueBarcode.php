<?php

namespace Webkul\Product\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Webkul\Product\Models\Packaging;
use Webkul\Product\Models\Product;

class UniqueBarcode implements ValidationRule
{
    public function __construct(
        protected mixed $companyId = null,
        protected mixed $ignore = null,
        protected string $owner = Product::class,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            return;
        }

        $ignoreId = $this->ignore instanceof Model ? $this->ignore->getKey() : $this->ignore;

        $existsIn = fn (Builder $query, bool $ignore): bool => $query
            ->whereRaw(db_dialect()->caseInsensitiveEquals('barcode'), [$value])
            ->when(
                $this->companyId === null,
                fn (Builder $scoped): Builder => $scoped->whereNull('company_id'),
                fn (Builder $scoped): Builder => $scoped->where('company_id', $this->companyId),
            )
            ->when($ignore && $ignoreId, fn (Builder $scoped): Builder => $scoped->whereKeyNot($ignoreId))
            ->exists();

        $taken = $existsIn(Product::withTrashed(), $this->owner === Product::class)
            || $existsIn(Packaging::query(), $this->owner === Packaging::class);

        if ($taken) {
            $fail(__('validation.unique', ['attribute' => Str::afterLast($attribute, '.')]));
        }
    }
}
