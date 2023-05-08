<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::get('{shortId}', [LinkController::class, 'redirect']);
