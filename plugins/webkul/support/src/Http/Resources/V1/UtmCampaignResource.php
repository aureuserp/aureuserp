<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class UtmCampaignResource extends JsonResource
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
            'name'              => $this->name,
            'title'             => $this->title,
            'color'             => $this->color,
            'is_active'         => $this->is_active,
            'is_auto_campaign'  => $this->is_auto_campaign,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'user'              => UserResource::make($this->whenLoaded('user')),
            'stage'             => UtmStageResource::make($this->whenLoaded('stage')),
            'company'           => CompanyResource::make($this->whenLoaded('company')),
            'creator'           => UserResource::make($this->whenLoaded('createdBy')),
        ];
    }
}
