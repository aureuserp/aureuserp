<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Collection;
use Webkul\PointOfSale\Events\PreparationTicketsRouted;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Printer;

class PreparationRouter
{
    public function route(Order $order): Collection
    {
        $order->loadMissing(['config.printers.categories', 'lines.product.posCategories']);

        if (! $order->config?->is_restaurant) {
            return collect();
        }

        $tickets = $order->config->printers
            ->map(fn (Printer $printer): array => $this->ticketFor($printer, $order))
            ->filter(fn (array $ticket): bool => ! empty($ticket['lines']))
            ->values();

        if ($tickets->isEmpty()) {
            return $tickets;
        }

        $this->markRouted($order, $tickets);

        PreparationTicketsRouted::dispatch($order, $tickets->all());

        return $tickets;
    }

    public function pendingLines(Order $order): Collection
    {
        return $order->lines->reject(
            fn (OrderLine $line): bool => (bool) $line->is_skipped_in_preparation
        );
    }

    protected function ticketFor(Printer $printer, Order $order): array
    {
        $categoryIds = $printer->categories->pluck('id');

        $lines = $this->pendingLines($order)
            ->filter(fn (OrderLine $line): bool => $categoryIds->isEmpty()
                || $line->product?->posCategories?->pluck('id')->intersect($categoryIds)->isNotEmpty())
            ->map(fn (OrderLine $line): array => [
                'line_id'       => $line->id,
                'name'          => $line->full_product_name ?? $line->product?->name,
                'qty'           => (float) $line->qty,
                'customer_note' => $line->customer_note,
            ])
            ->values()
            ->all();

        return [
            'printer_id'   => $printer->id,
            'printer_name' => $printer->name,
            'printer_type' => $printer->printer_type,
            'proxy_ip'     => $printer->proxy_ip,
            'order'        => $order->name ?? $order->reference,
            'table'        => $order->table?->table_number,
            'lines'        => $lines,
        ];
    }

    protected function markRouted(Order $order, Collection $tickets): void
    {
        $lineIds = $tickets
            ->flatMap(fn (array $ticket): array => array_column($ticket['lines'], 'line_id'))
            ->unique();

        OrderLine::withoutGlobalScopes()
            ->whereIn('id', $lineIds)
            ->update(['is_skipped_in_preparation' => true]);
    }
}
