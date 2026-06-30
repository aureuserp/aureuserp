<?php

namespace Webkul\Support\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\Support\Services\CompanyContext;

class CompanyContextController extends Controller
{
    public function set(Request $request)
    {
        $ids = array_map('intval', (array) $request->input('companies', []));

        app(CompanyContext::class)->setActive($ids);

        return back();
    }
}
