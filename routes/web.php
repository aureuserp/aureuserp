<?php

use Illuminate\Support\Facades\Route;

if (! request()->getRequestUri() == '/login') {
    Route::redirect('/login', '/admin/login')
        ->name('login');
}
