<?php

namespace App\Http\Controllers;

use App\Services\ScraperService\CurrencyScraperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CurrencyController extends Controller
{


    public function __construct(
        protected CurrencyScraperService $currencyScraperService
    )
    {
    }


    public function fetchCurrencies(Request $request): JsonResponse
    {
        $data = $this->currencyScraperService->fetchCurrenciesByCodeOrNumber(['USD', 'EUR']);

        return response()->json($data, Response::HTTP_ACCEPTED);
    }
}
