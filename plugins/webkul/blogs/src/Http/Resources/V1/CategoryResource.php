<?php

namespace Webkul\Blog\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'sub_title'        => $this->sub_title,
            'slug'             => $this->slug,
            'image'            => $this->image,
            'image_url'        => $this->image_url,
            'meta_title'       => $this->meta_title,
            'meta_keywords'    => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'creator_id'       => $this->creator_id,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'deleted_at'       => $this->deleted_at,
            'creator'          => new UserResource($this->whenLoaded('creator')),
            'posts'            => PostResource::collection($this->whenLoaded('posts')),
        ];
    }
}
