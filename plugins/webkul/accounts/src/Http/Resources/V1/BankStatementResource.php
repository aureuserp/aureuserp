<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class BankStatementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'company_id'        => $this->company_id,
            'journal_id'        => $this->journal_id,
            'created_by'        => $this->created_by,
            'name'              => $this->name,
            'reference'         => $this->reference,
            'first_line_index'  => $this->first_line_index,
            'date'              => $this->date,
            'balance_start'     => $this->balance_start,
            'balance_end'       => $this->balance_end,
            'balance_end_real'  => $this->balance_end_real,
            'is_completed'      => $this->is_completed,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'company'           => new CompanyResource($this->whenLoaded('company')),
            'journal'           => new JournalResource($this->whenLoaded('journal')),
            'createdBy'         => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}
