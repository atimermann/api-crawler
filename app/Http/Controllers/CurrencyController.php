<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencySearchRequest;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CurrencyController extends Controller
{


    public function __construct(
        protected CurrencyService $CurrencyService
    )
    {
    }

    /**
     * Handles the incoming request to fetch currencies based on ISO codes or numbers.
     *
     * @param CurrencySearchRequest $request The validated and normalized request.
     * @return JsonResponse Returns the fetched currency data as a JSON response.
     */
    public function fetchCurrencies(CurrencySearchRequest $request): JsonResponse
    {
        $searchItems = $request->normalizedInput();
        $data = $this->CurrencyService->fetchCurrenciesByCodeOrNumber($searchItems);
        return response()->json($data, Response::HTTP_CREATED);
    }
}
