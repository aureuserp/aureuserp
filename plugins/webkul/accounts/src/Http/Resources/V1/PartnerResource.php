<?php

namespace Webkul\Account\Http\Resources\V1;

use Illuminate\Http\Request;
use Webkul\Partner\Http\Resources\V1\PartnerResource as BasePartnerResource;

class PartnerResource extends BasePartnerResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        return array_merge($data, [
            'message_bounce'                              => $this->message_bounce,
            'supplier_rank'                               => $this->supplier_rank,
            'customer_rank'                               => $this->customer_rank,
            'invoice_warning'                             => $this->invoice_warning,
            'autopost_bills'                              => $this->autopost_bills,
            'credit_limit'                                => $this->credit_limit,
            'ignore_abnormal_invoice_date'                => $this->ignore_abnormal_invoice_date,
            'ignore_abnormal_invoice_amount'              => $this->ignore_abnormal_invoice_amount,
            'invoice_sending_method'                      => $this->invoice_sending_method,
            'invoice_edi_format_store'                    => $this->invoice_edi_format_store,
            'trust'                                       => $this->trust,
            'invoice_warn_msg'                            => $this->invoice_warn_msg,
            'debit_limit'                                 => $this->debit_limit,
            'peppol_endpoint'                             => $this->peppol_endpoint,
            'peppol_eas'                                  => $this->peppol_eas,
            'sale_warn'                                   => $this->sale_warn,
            'comment'                                     => $this->comment,
            'sale_warn_msg'                               => $this->sale_warn_msg,
            'property_account_payable_id'                 => $this->property_account_payable_id,
            'property_account_receivable_id'              => $this->property_account_receivable_id,
            'property_account_position_id'                => $this->property_account_position_id,
            'property_payment_term_id'                    => $this->property_payment_term_id,
            'property_supplier_payment_term_id'           => $this->property_supplier_payment_term_id,
            'property_outbound_payment_method_line_id'    => $this->property_outbound_payment_method_line_id,
            'property_inbound_payment_method_line_id'     => $this->property_inbound_payment_method_line_id,
            'propertyAccountPayable'                      => new AccountResource($this->whenLoaded('propertyAccountPayable')),
            'propertyAccountReceivable'                   => new AccountResource($this->whenLoaded('propertyAccountReceivable')),
            'propertyAccountPosition'                     => new FiscalPositionResource($this->whenLoaded('propertyAccountPosition')),
            'propertyPaymentTerm'                         => new PaymentTermResource($this->whenLoaded('propertyPaymentTerm')),
            'propertySupplierPaymentTerm'                 => new PaymentTermResource($this->whenLoaded('propertySupplierPaymentTerm')),
            'propertyOutboundPaymentMethodLine'           => new PaymentMethodLineResource($this->whenLoaded('propertyOutboundPaymentMethodLine')),
            'propertyInboundPaymentMethodLine'            => new PaymentMethodLineResource($this->whenLoaded('propertyInboundPaymentMethodLine')),
        ]);
    }
}
