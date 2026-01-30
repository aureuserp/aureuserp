<?php

namespace Webkul\Field\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class FieldResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'code'              => $this->code,
            'name'              => $this->name,
            'type'              => $this->type,
            'input_type'        => $this->input_type,
            'is_multiselect'    => (bool) $this->is_multiselect,
            'datalist'          => $this->datalist,
            'options'           => $this->options,
            'form_settings'     => $this->form_settings,
            'use_in_table'      => $this->use_in_table,
            'table_settings'    => $this->table_settings,
            'infolist_settings' => $this->infolist_settings,
            'sort'              => $this->sort,
            'customizable_type' => $this->customizable_type,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'deleted_at'        => $this->deleted_at,
        ];
    }
}
