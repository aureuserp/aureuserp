<?php

namespace Webkul\Partner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Partner\Enums\AccountType;

class PartnerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'account_type'     => 'required|string|in:'.implode(',', array_column(AccountType::cases(), 'value')),
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:20',
            'mobile'           => 'nullable|string|max:20',
            'avatar'           => 'nullable|string|max:255',
            'job_title'        => 'nullable|string|max:255',
            'website'          => 'nullable|url|max:255',
            'tax_id'           => 'nullable|string|max:255',
            'company_registry' => 'nullable|string|max:255',
            'reference'        => 'nullable|string|max:255',
            'color'            => 'nullable|string|max:7',
            'street1'          => 'nullable|string|max:255',
            'street2'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'zip'              => 'nullable|string|max:20',
            'state_id'         => 'nullable|integer|exists:states,id',
            'country_id'       => 'nullable|integer|exists:countries,id',
            'parent_id'        => 'nullable|integer|exists:partners_partners,id',
            'title_id'         => 'nullable|integer|exists:partners_titles,id',
            'company_id'       => 'nullable|integer|exists:companies,id',
            'industry_id'      => 'nullable|integer|exists:partners_industries,id',
            'user_id'          => 'nullable|integer|exists:users,id',
        ];

        // On update, make all fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = array_map(function ($rule) {
                if (is_string($rule) && str_starts_with($rule, 'required')) {
                    return str_replace('required', 'sometimes|required', $rule);
                }

                return $rule;
            }, $rules);
        }

        return $rules;
    }

    /**
     * Get body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'account_type' => [
                'description' => 'Account type',
                'example'     => AccountType::INDIVIDUAL->value,
            ],
            'name' => [
                'description' => 'Partner name (max 255 characters).',
                'example'     => 'Acme Corporation',
            ],
            'email' => [
                'description' => 'Email address (max 255 characters).',
                'example'     => 'contact@acme.com',
            ],
            'phone' => [
                'description' => 'Phone number (max 20 characters).',
                'example'     => '+1234567890',
            ],
            'mobile' => [
                'description' => 'Mobile number (max 20 characters).',
                'example'     => '+1234567890',
            ],
            'avatar' => [
                'description' => 'Avatar file path (max 255 characters).',
                'example'     => 'avatars/partner.jpg',
            ],
            'job_title' => [
                'description' => 'Job title (max 255 characters).',
                'example'     => 'CEO',
            ],
            'website' => [
                'description' => 'Website URL (max 255 characters).',
                'example'     => 'https://acme.com',
            ],
            'tax_id' => [
                'description' => 'Tax identification number (max 255 characters).',
                'example'     => 'TAX-123456',
            ],
            'company_registry' => [
                'description' => 'Company registry number (max 255 characters).',
                'example'     => 'REG-123456',
            ],
            'reference' => [
                'description' => 'Reference code (max 255 characters).',
                'example'     => 'PART-001',
            ],
            'color' => [
                'description' => 'Color in hex format (max 7 characters).',
                'example'     => '#FF5733',
            ],
            'street1' => [
                'description' => 'Street address line 1 (max 255 characters).',
                'example'     => '123 Main Street',
            ],
            'street2' => [
                'description' => 'Street address line 2 (max 255 characters).',
                'example'     => 'Suite 100',
            ],
            'city' => [
                'description' => 'City (max 255 characters).',
                'example'     => 'New York',
            ],
            'zip' => [
                'description' => 'Postal/ZIP code (max 20 characters).',
                'example'     => '10001',
            ],
            'state_id' => [
                'description' => 'State ID.',
                'example'     => 9,
            ],
            'country_id' => [
                'description' => 'Country ID.',
                'example'     => 233,
            ],
            'parent_id' => [
                'description' => 'Parent partner ID (for addresses and contacts).',
                'example'     => null,
            ],
            'title_id' => [
                'description' => 'Title ID.',
                'example'     => 1,
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'industry_id' => [
                'description' => 'Industry ID.',
                'example'     => 1,
            ],
            'user_id' => [
                'description' => 'Associated user ID/Responsible Sale user.',
                'example'     => 1,
            ],
        ];
    }
}
