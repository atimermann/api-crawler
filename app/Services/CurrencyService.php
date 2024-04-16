<?php

namespace App\Services;

use App\Repositories\Contracts\CurrencyRepositoryInterface;
use App\Services\ScraperService\CurrencyScraperService;
use Exception;

class CurrencyService
{

    /**
     * Constructs the service with dependencies.
     *
     * @param CurrencyScraperService $crawlerService The service for scraping currency data.
     * @param CurrencyRepositoryInterface $currencyRepository The repository for accessing currency data.
     */
    public function __construct(
        protected CurrencyScraperService      $crawlerService,
        protected CurrencyRepositoryInterface $currencyRepository
    )
    {
    }

    /**
     * Fetches currency data by codes or numbers from crawler, database or cache
     *
     * @param array $codeAndNumberToSearch Array of currency codes and numbers to search.
     * @return array Returns an array of merged and sanitized currency data.
     * @throws Exception
     */
    public function fetchCurrenciesByCodeOrNumber(array $codeAndNumberToSearch): array
    {

        $existingCurrencies = $this->currencyRepository->getCurrenciesByCodeAndNumber($codeAndNumberToSearch);

        $existingCodeAndNumber = $this->flattenCurrencyCodeAndNumber($existingCurrencies);

        $codeAndNumberToScrapping = $this->removeValuesFromArray($codeAndNumberToSearch, $existingCodeAndNumber);

        $scrappingData = $this->crawlerService->fetchCurrenciesByCodeOrNumber($codeAndNumberToScrapping);

        $this->currencyRepository->saveCurrencies($scrappingData);


        return [
            "data" => $this->removeUnwantedFields(array_merge($existingCurrencies, $scrappingData)),
            "info" => $this->createStatisticFetch($existingCurrencies, $scrappingData)
        ];

    }

    /**
     * Generates statistics for data fetched from different sources.
     *
     * @param array $scrappingData Data fetched from the crawler.
     * @param array $existingCurrencies Data fetched from the database.
     *
     * @return array Statistics about the data sources.
     */
    protected function createStatisticFetch($scrappingData, $existingCurrencies): array
    {
        return [
            "fetchFromCrawler" => count($scrappingData),
            "fetchFromDatabase" => count($existingCurrencies),
            "fetchFromCache" => 0
        ];
    }

    /**
     * Removes specified values from an array.
     *
     * @param array $originalArray The original array.
     * @param array $valuesToRemove The values to be removed.
     * @return array The array after removing specified values.
     */
    protected function removeValuesFromArray(array $originalArray, array $valuesToRemove): array
    {
        return array_values(array_diff($originalArray, $valuesToRemove));
    }

    /**
     * Flattens currency codes and numbers into a single array.
     *
     * @param array $currencies Array of currencies.
     * @return array An array containing currency codes and numbers.
     */
    protected function flattenCurrencyCodeAndNumber(array $currencies): array
    {
        $codes = [];
        $numbers = [];

        foreach ($currencies as $currency) {
            $codes[] = $currency['code'];
            $numbers[] = (string)$currency['number'];
        }

        return array_merge($codes, $numbers);
    }

    /**
     * Removes unwanted fields from currencies and their locations.
     *
     * @param array $currencies Array of currency data.
     * @return array The array with unwanted fields removed.
     */
    protected function removeUnwantedFields(array $currencies): array
    {
        foreach ($currencies as &$currency) {
            unset($currency['id']);
            if (isset($currency['locations']) && is_array($currency['locations'])) {
                foreach ($currency['locations'] as &$location) {
                    unset($location['currency_id']);
                }
            }
        }

        unset($currency, $location);
        return $currencies;
    }

}
