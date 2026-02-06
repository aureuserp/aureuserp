<?php

namespace Webkul\Website\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class PageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'content'           => $this->content,
            'slug'              => $this->slug,
            'is_published'      => (bool) $this->is_published,
            'published_at'      => $this->published_at,
            'is_header_visible' => (bool) $this->is_header_visible,
            'is_footer_visible' => (bool) $this->is_footer_visible,
            'meta_title'        => $this->meta_title,
            'meta_keywords'     => $this->meta_keywords,
            'meta_description'  => $this->meta_description,
            'creator_id'        => $this->creator_id,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'deleted_at'        => $this->deleted_at,
            'creator'           => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
