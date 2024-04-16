<?php

use App\Http\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::post('/currencies', [CurrencyController::class, 'fetchCurrencies']);
