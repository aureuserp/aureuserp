<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Webkul\Product\Http\Resources\V1\ProductResource as BaseProductResource;

class ProductResource extends BaseProductResource
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
            'property_account_income_id'   => $this->property_account_income_id,
            'property_account_expense_id'  => $this->property_account_expense_id,
            'image'                        => $this->image,
            'service_type'                 => $this->service_type,
            'sale_line_warn'               => $this->sale_line_warn,
            'expense_policy'               => $this->expense_policy,
            'invoice_policy'               => $this->invoice_policy,
            'sale_line_warn_msg'           => $this->sale_line_warn_msg,
            'sales_ok'                     => $this->sales_ok,
            'purchase_ok'                  => $this->purchase_ok,
            'propertyAccountIncome'        => new AccountResource($this->whenLoaded('propertyAccountIncome')),
            'propertyAccountExpense'       => new AccountResource($this->whenLoaded('propertyAccountExpense')),
        ]);
    }
}
