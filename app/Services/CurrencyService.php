<?php

namespace App\Services;

use App\Repositories\Contracts\CurrencyRepositoryInterface;
use App\Services\ScraperService\CurrencyScraperService;

class CurrencyService
{

    public function __construct(
        protected CurrencyScraperService      $crawlerService,
        protected CurrencyRepositoryInterface $currencyRepository
    )
    {
    }

    public function fetchCurrenciesByCodeOrNumber(array $searchItems)
    {

        $existingItems = $this->currencyRepository->findExistingItems($searchItems);

        return 'ok';
    }

//    /**
//     * Fetch currencies by ISO codes or numbers.
//     *
//     * @param array $searchItems Items to search.
//     * @return array The data of the fetched currencies.
//     */
//    public function fetchCurrenciesByCodeOrNumber(array $searchItems): array
//    {
//        $currencies = Currency::whereIn('code', $searchItems)
//            ->orWhereIn('number', $searchItems)
//            ->with('locations')
//            ->get()
//            ->toArray();
//
//        return $currencies;
//    }
//
//    /**
//     * Save currency data in the database.
//     *
//     * @param array $currencyData Data of the currency to save.
//     * @return Currency The saved currency model instance.
//     */
//    public function saveCurrency(array $currencyData): Currency
//    {
//        $currency = new Currency();
//        $currency->code = $currencyData['code'];
//        $currency->number = $currencyData['number'];
//        $currency->decimal = $currencyData['decimal'];
//        $currency->name = $currencyData['name'];
//        $currency->save();
//
//        foreach ($currencyData['locations'] as $locationData) {
//            $location = new Location();
//            $location->currency_id = $currency->id;
//            $location->name = $locationData['name'];
//            $location->icon = $locationData['icon'];
//            $location->save();
//        }
//
//        return $currency;
//    }
}
