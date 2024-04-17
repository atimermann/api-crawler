<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencySearchRequest;
use App\Services\CurrencyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 *
 * @OA\Schema(
 * schema="CurrencyRequestSchema",
 * type="object",
 * @OA\Property(property="code_list", type="array", @OA\Items(type="string"), description="List of ISO currency codes"),
 * @OA\Property(property="number_list", type="array", @OA\Items(type="integer"), description="List of ISO currency numbers"),
 * @OA\Property(property="code", type="string", description="Single ISO currency code"),
 * @OA\Property(property="number", type="integer", description="Single ISO currency number")
 * )
 *
 * @OA\Schema(
 *    schema="CurrencyData",
 *    type="object",
 *    required={"code", "number", "decimal", "name", "locations"},
 *    @OA\Property(property="code", type="string", description="ISO currency code"),
 *    @OA\Property(property="number", type="integer", description="ISO currency number"),
 *    @OA\Property(property="decimal", type="integer", description="Number of decimal places"),
 *    @OA\Property(property="name", type="string", description="Name of the currency"),
 *    @OA\Property(
 *      property="locations",
 *      type="array",
 *      @OA\Items(type="object",
 *        @OA\Property(property="name", type="string", description="Location name"),
 *        @OA\Property(property="icon", type="string", description="Location flag icon URL")
 *      )
 *    )
 *  )
 * @OA\Schema(
 *   schema="RequestInfo",
 *   type="object",
 *   required={"fetchFromCrawler", "fetchFromDatabase", "fetchFromCache", "length"},
 *   @OA\Property(property="fetchFromCrawler", type="integer", description="The total number of currencies fetched directly from the crawler."),
 *   @OA\Property(property="fetchFromDatabase", type="integer", description="The total number of currencies fetched from the database."),
 *   @OA\Property(property="fetchFromCache", type="integer", description="The total number of currencies retrieved from the cache."),
 *   @OA\Property(property="length", type="integer", description="The total number of currencies fetched.")
 * )
 *

 *
 */



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
     * @OA\Post(
     * path="/api/currencies",
     * summary="Fetch currency data based on ISO codes or numbers",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass currency codes or numbers",
     *    @OA\JsonContent(
     *       required={"code_list","number_list"},
     *
     *       @OA\Examples(
     *           summary="An example of currency codes and numbers",
     *           example = "codeList",
     *           value={
     *              "code_list": {"USD", "EUR"},
     *              "number_list": {600, 946},
     *              "code": "TRY",
                    "number": "834"
     *           }
     *       ),
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CurrencyRequestSchema"))
     *    )
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Currency resource successfully",
     *    @OA\JsonContent(
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CurrencyData")),
     *       @OA\Property(property="info", type="array", @OA\Items(ref="#/components/schemas/RequestInfo"))
     *    )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request"
     * ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error"
     * )
     * )
     *
     * @param CurrencySearchRequest $request The validated and normalized request.
     * @return JsonResponse Returns the fetched currency data as a JSON response.
     * @throws Exception
     */
    public function fetchCurrencies(CurrencySearchRequest $request): JsonResponse
    {
        $searchItems = $request->normalizedInput();
        $data = $this->CurrencyService->fetchCurrenciesByCodeOrNumber($searchItems);
        return response()->json($data, Response::HTTP_CREATED);
    }
}
