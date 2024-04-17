<?php

namespace App\Services;

use App\Repositories\Contracts\CurrencyRepositoryInterface;
use App\Services\ScraperService\CurrencyScraperService;
use Exception;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{

    private int $cacheTime = 3600;

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
     *
     * @return array Returns an array of merged and sanitized currency data.
     * @throws Exception
     */
    public function fetchCurrenciesByCodeOrNumber(array $codeAndNumberToSearch): array
    {

        [$cachedCurrencies, $codeAndNumberToFetch] = $this->fetchCurrenciesByCodeOrNumberFromCache($codeAndNumberToSearch);
        [$fetchedCurrencies, $codeAndNumberToCrawler] = $this->fetchCurrenciesByCodeOrNumberFromDatabase($codeAndNumberToFetch);
        $crawledCurrencies = $this->fetchCurrenciesByCodeOrNumberFromCrawler($codeAndNumberToCrawler);

        $this->currencyRepository->saveCurrencies($crawledCurrencies);
        $this->cacheFetchedCurrencies(array_merge($fetchedCurrencies, $crawledCurrencies));


        return [
            "data" => array_merge($cachedCurrencies, $fetchedCurrencies, $crawledCurrencies),
            "info" => $this->createStatisticFetch($cachedCurrencies, $fetchedCurrencies, $crawledCurrencies)
        ];
    }

    /**
     * Fetches currencies from cache based on specified codes or numbers.     *
     * @param array $codeAndNumberToSearch Array of currency codes and numbers to search in cache.
     *
     * @return array Tuple containing cached currency data and the codes/numbers to be fetched from the database.
     */
    private function fetchCurrenciesByCodeOrNumberFromCache(array $codeAndNumberToSearch): array
    {

        $cachedCurrencyData = [];
        $codeAndNumberToCrawler = [];

        foreach ($codeAndNumberToSearch as $item) {
            $cacheKey = 'currency_' . $item;
            $cachedCurrency = Cache::get($cacheKey);

            if ($cachedCurrency) {
                $cachedCurrencyData[] = $cachedCurrency;
            } else {
                $codeAndNumberToCrawler[] = $item;
            }
        }
        return [$cachedCurrencyData, $codeAndNumberToCrawler];
    }


    /**
     * Fetches currencies from the database based on the specified codes or numbers not found in cache.
     *
     * @param array $codeAndNumberToFetch Array of currency codes and numbers to search in the database.
     * @return array Tuple containing fetched currency data from database and the codes/numbers to be fetched from the crawler.
     */
    private function fetchCurrenciesByCodeOrNumberFromDatabase(array $codeAndNumberToFetch): array
    {
        if (empty($codeAndNumberToFetch)) {
            return [[], []];
        }

        $fetchedCurrencies = $this->currencyRepository->getCurrenciesByCodeAndNumber($codeAndNumberToFetch);

        $fetchedCodeAndNumber = $this->flattenCurrencyCodeAndNumber($fetchedCurrencies);
        $codeAndNumberToCrawler = $this->removeValuesFromArray($codeAndNumberToFetch, $fetchedCodeAndNumber);

        return [$this->removeUnwantedFields($fetchedCurrencies), $codeAndNumberToCrawler];
    }


    /**
     * Fetches currencies from an external crawler based on specified codes or numbers not found in the database.
     *
     * @param array $codeAndNumberToCrawler Array of currency codes and numbers to search via crawler.
     *
     * @return array Array of currencies fetched from the crawler.
     */
    private function fetchCurrenciesByCodeOrNumberFromCrawler(array $codeAndNumberToCrawler): array
    {
        if (empty($codeAndNumberToCrawler)) {
            return [];
        }

        return $this->crawlerService->fetchCurrenciesByCodeOrNumber($codeAndNumberToCrawler);
    }

    /**
     * Caches the fetched currencies. Each currency is cached under its code and number.
     *
     * @param array $currencies Array of currency data to be cached.
     */
    private function cacheFetchedCurrencies(array $currencies): void
    {

        foreach ($currencies as $currency) {
            $cacheKey = 'currency_' . $currency['code'];
            Cache::put($cacheKey, $currency, $this->cacheTime);
            $cacheKey = 'currency_' . $currency['number'];
            Cache::put($cacheKey, $currency, $this->cacheTime);
        }
    }

    /**
     * Generates statistics for data fetched from different sources.
     *
     * @param array $cachedCurrencies Data fetched from the cache.
     * @param array $fetchedCurrencies Data fetched from the database.
     * @param array $crawledCurrencies Data fetched from the crawler.
     *
     * @return array Statistics about the data sources.
     */
    protected function createStatisticFetch(array $cachedCurrencies, array $fetchedCurrencies, array $crawledCurrencies): array
    {
        return [
            "fetchFromCrawler" => count($crawledCurrencies),
            "fetchFromDatabase" => count($fetchedCurrencies),
            "fetchFromCache" => count($cachedCurrencies),
            "length" => count($cachedCurrencies) + count($fetchedCurrencies) + count($crawledCurrencies)
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
