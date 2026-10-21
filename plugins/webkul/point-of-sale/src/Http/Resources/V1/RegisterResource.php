<?php

namespace Webkul\PointOfSale\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\PointOfSale\Facades\PointOfSale;

class RegisterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $live = PointOfSale::liveSessionFor($this->resource);

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'code'           => $this->code,
            'is_active'      => (bool) $this->is_active,
            'is_restaurant'  => (bool) $this->is_restaurant,
            'live_session'   => $live ? [
                'id'    => $live->id,
                'state' => $live->state,
                'name'  => $live->name,
                'user'  => $live->user?->name,
            ] : null,
            'is_discardable' => $live ? PointOfSale::isSessionDiscardable($live) : false,
        ];
    }
}
