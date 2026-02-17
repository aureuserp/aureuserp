<?php

namespace Webkul\Blog\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'sub_title'        => $this->sub_title,
            'content'          => $this->content,
            'slug'             => $this->slug,
            'image'            => $this->image,
            'image_url'        => $this->image_url,
            'author_name'      => $this->author_name,
            'is_published'     => (bool) $this->is_published,
            'published_at'     => $this->published_at,
            'visits'           => $this->visits,
            'meta_title'       => $this->meta_title,
            'meta_keywords'    => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'reading_time'     => $this->reading_time,
            'category_id'      => $this->category_id,
            'author_id'        => $this->author_id,
            'creator_id'       => $this->creator_id,
            'last_editor_id'   => $this->last_editor_id,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'deleted_at'       => $this->deleted_at,
            'category'         => new CategoryResource($this->whenLoaded('category')),
            'author'           => new UserResource($this->whenLoaded('author')),
            'creator'          => new UserResource($this->whenLoaded('creator')),
            'lastEditor'       => new UserResource($this->whenLoaded('lastEditor')),
            'tags'             => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
