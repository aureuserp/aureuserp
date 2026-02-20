<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Webkul\Account\Http\Resources\V1\ProductResource as BaseProductResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class ProductResource extends BaseProductResource
{
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        return array_merge($data, [
            'is_storable'         => (bool) $this->is_storable,
            'tracking'            => $this->tracking?->value,
            'use_expiration_date' => (bool) $this->use_expiration_date,
            'sale_delay'          => $this->sale_delay !== null ? (float) $this->sale_delay : null,
            'expiration_time'     => $this->expiration_time !== null ? (float) $this->expiration_time : null,
            'use_time'            => $this->use_time !== null ? (float) $this->use_time : null,
            'removal_time'        => $this->removal_time !== null ? (float) $this->removal_time : null,
            'alert_time'          => $this->alert_time !== null ? (float) $this->alert_time : null,
            'responsible_id'      => $this->responsible_id,
            'responsible'         => UserResource::make($this->whenLoaded('responsible')),
            'routes'              => RouteResource::collection($this->whenLoaded('routes')),
        ]);
    }
}
