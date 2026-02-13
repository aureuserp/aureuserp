<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'log_name'     => $this->log_name,
            'description'  => $this->description,
            'subject_type' => $this->subject_type,
            'subject_id'   => $this->subject_id,
            'event'        => $this->event,
            'causer_type'  => $this->causer_type,
            'causer_id'    => $this->causer_id,
            'properties'   => $this->properties,
            'changes'      => $this->changes,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
