<?php

use Illuminate\Support\Facades\Route;
use Webkul\Support\Http\Controllers\ImageController;

Route::get('img/{path}', ImageController::class)
    ->where('path', '.*')
    ->name('support.image');
