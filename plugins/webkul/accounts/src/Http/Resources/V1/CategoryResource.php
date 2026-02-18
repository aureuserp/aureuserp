<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Webkul\Product\Http\Resources\V1\CategoryResource as BaseCategory;

class CategoryResource extends BaseCategory
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        return array_merge($data, [
            'property_account_income_id'       => $this->property_account_income_id,
            'property_account_expense_id'      => $this->property_account_expense_id,
            'property_account_down_payment_id' => $this->property_account_down_payment_id,
            'propertyAccountIncome'            => new AccountResource($this->whenLoaded('propertyAccountIncome')),
            'propertyAccountExpense'           => new AccountResource($this->whenLoaded('propertyAccountExpense')),
            'propertyAccountDownPayment'       => new AccountResource($this->whenLoaded('propertyAccountDownPayment')),
            'products'                         => ProductResource::collection($this->whenLoaded('products')),
        ]);
    }
}
