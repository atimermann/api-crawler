<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CurrencyController extends Controller
{

    public function fetchCurrencies(Request $request): JsonResponse
    {
        return response()->json('ok', Response::HTTP_ACCEPTED);
    }
}
