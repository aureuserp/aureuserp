<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/admin-login', '/admin/login')
    ->name('login');
