<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class OperationTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                                  => $this->id,
            'name'                                => $this->name,
            'type'                                => $this->type?->value,
            'sort'                                => $this->sort,
            'sequence_code'                       => $this->sequence_code,
            'reservation_method'                  => $this->reservation_method?->value,
            'reservation_days_before'             => $this->reservation_days_before,
            'reservation_days_before_priority'    => $this->reservation_days_before_priority,
            'product_label_format'                => $this->product_label_format,
            'lot_label_format'                    => $this->lot_label_format,
            'package_label_to_print'              => $this->package_label_to_print,
            'barcode'                             => $this->barcode,
            'create_backorder'                    => $this->create_backorder?->value,
            'move_type'                           => $this->move_type?->value,
            'show_entire_packs'                   => (bool) $this->show_entire_packs,
            'use_create_lots'                     => (bool) $this->use_create_lots,
            'use_existing_lots'                   => (bool) $this->use_existing_lots,
            'print_label'                         => (bool) $this->print_label,
            'show_operations'                     => (bool) $this->show_operations,
            'auto_show_reception_report'          => (bool) $this->auto_show_reception_report,
            'auto_print_delivery_slip'            => (bool) $this->auto_print_delivery_slip,
            'auto_print_return_slip'              => (bool) $this->auto_print_return_slip,
            'auto_print_product_labels'           => (bool) $this->auto_print_product_labels,
            'auto_print_lot_labels'               => (bool) $this->auto_print_lot_labels,
            'auto_print_reception_report'         => (bool) $this->auto_print_reception_report,
            'auto_print_reception_report_labels'  => (bool) $this->auto_print_reception_report_labels,
            'auto_print_packages'                 => (bool) $this->auto_print_packages,
            'auto_print_package_label'            => (bool) $this->auto_print_package_label,
            'return_operation_type'               => OperationTypeResource::make($this->whenLoaded('returnOperationType')),
            'source_location'                     => LocationResource::make($this->whenLoaded('sourceLocation')),
            'destination_location'                => LocationResource::make($this->whenLoaded('destinationLocation')),
            'warehouse'                           => WarehouseResource::make($this->whenLoaded('warehouse')),
            'company'                             => CompanyResource::make($this->whenLoaded('company')),
            'creator'                             => UserResource::make($this->whenLoaded('creator')),
            'created_at'                          => $this->created_at,
            'updated_at'                          => $this->updated_at,
            'deleted_at'                          => $this->deleted_at,
        ];
    }
}
