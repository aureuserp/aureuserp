<?php

namespace Webkul\Support\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Webkul\Support\Services\CompanyContext;

class WithinAllowedCompanies implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            return;
        }

        $context = app(CompanyContext::class);

        if (! $context->internalUser()) {
            return;
        }

        if ($context->seesAllCompanies()) {
            return;
        }

        if (! in_array((int) $value, $context->allowedIds(), true)) {
            $fail(__('support::support.cross-company.not-allowed'));
        }
    }
}
