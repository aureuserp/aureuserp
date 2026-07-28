<?php

namespace Webkul\Sale\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91Service
{
    public function send(string $mobile, string $message): bool
    {
        if (empty($mobile) || empty(config('services.msg91.auth_key'))) {
            return false;
        }

        $response = Http::get('https://api.msg91.com/api/sendhttp.php', [
            'authkey' => config('services.msg91.auth_key'),
            'mobiles' => $mobile,
            'message' => $message,
            'sender'  => config('services.msg91.sender_id'),
            'route'   => config('services.msg91.route'),
        ]);

        if (! $response->successful()) {
            Log::error('MSG91 SMS failed', [
                'mobile'   => $mobile,
                'response' => $response->body(),
            ]);

            return false;
        }

        return true;
    }
}
