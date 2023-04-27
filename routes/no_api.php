<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::get('{short-link}', [LinkController::class, 'redirect']);
