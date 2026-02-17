<?php

namespace Webkul\Chatter\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\ActivityTypeResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class MessageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'type'             => $this->type,
            'name'             => $this->name,
            'subject'          => $this->subject,
            'body'             => $this->body,
            'summary'          => $this->summary,
            'is_internal'      => (bool) $this->is_internal,
            'date_deadline'    => $this->date_deadline,
            'pinned_at'        => $this->pinned_at,
            'log_name'         => $this->log_name,
            'event'            => $this->event,
            'properties'       => $this->properties,
            'messageable_type' => $this->messageable_type,
            'messageable_id'   => $this->messageable_id,
            'company_id'       => $this->company_id,
            'activity_type_id' => $this->activity_type_id,
            'assigned_to'      => $this->assigned_to,
            'causer_type'      => $this->causer_type,
            'causer_id'        => $this->causer_id,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'company'          => new CompanyResource($this->whenLoaded('company')),
            'activityType'     => new ActivityTypeResource($this->whenLoaded('activityType')),
            'assignedTo'       => new UserResource($this->whenLoaded('assignedTo')),
        ];
    }
}
