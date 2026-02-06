<?php

namespace Webkul\Chatter\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class AttachmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'file_size'          => $this->file_size,
            'messageable'        => $this->messageable,
            'file_path'          => $this->file_path,
            'original_file_name' => $this->original_file_name,
            'mime_type'          => $this->mime_type,
            'url'                => $this->url,
            'company_id'         => $this->company_id,
            'creator_id'         => $this->creator_id,
            'message_id'         => $this->message_id,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'company'            => new CompanyResource($this->whenLoaded('company')),
            'createdBy'          => new UserResource($this->whenLoaded('createdBy')),
            'message'            => new MessageResource($this->whenLoaded('message')),
        ];
    }
}
