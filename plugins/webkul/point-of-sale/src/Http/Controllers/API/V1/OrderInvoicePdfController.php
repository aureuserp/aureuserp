<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Account\Models\Move;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Support\PosAccess;
use Webkul\Support\Traits\PDFHandler;

class OrderInvoicePdfController extends Controller
{
    use PDFHandler;

    public function show(string $order): Response
    {
        $model = Order::withoutGlobalScopes()->findOrFail($order);

        abort_unless(PosAccess::reachesOrder($model), 403);

        abort_unless($model->account_move_id !== null, 404);

        $move = Move::withoutGlobalScopes()->findOrFail($model->account_move_id);

        return $this->downloadPDF(
            view('accounts::invoice/actions/preview.index', ['record' => $move])->render(),
            'invoice-'.str_replace('/', '_', (string) ($model->name ?? $model->id)),
        );
    }
}
