<?php

namespace App\Services\ScraperService;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class CurrencyScraperService
{

    private const SCRAPPER_URL = 'https://pt.wikipedia.org/wiki/ISO_4217';
    protected HttpBrowser $browser;
    protected Crawler $crawler;

    public function __construct()
    {
        $this->browser = new HttpBrowser(HttpClient::create());
        $this->crawler = $this->browser->request('GET', self::SCRAPPER_URL);
    }

    /**
     * Fetches currency data filtered by currency codes or numbers from a specific webpage.
     *
     * @param string[] $searchItems An array of currency codes or numbers to search for.
     *
     * @return array An array of extracted currency data that matches the search criteria.
     */
    public function fetchCurrenciesByCodeOrNumber(array $searchItems): array
    {
        $results = [];

        $currencyNodes = $this->extractCurrenciesNodes($this->crawler);
        $currentNodes = $this->filterSearchItems($currencyNodes, $searchItems);

        foreach ($currentNodes as $currentNode) {
            $results[] = $this->extractCurrencyData($currentNode);
        }

        return $results;

    }

    /**
     * Extracts currency nodes from the DOM.
     *
     * @param Crawler $crawlerDom The DOM crawler instance.
     *
     * @return Crawler A Crawler instance containing currency rows.
     */
    protected function extractCurrenciesNodes(Crawler $crawlerDom): Crawler
    {
        return $crawlerDom
            ->filterXPath('//*[@id="mw-content-text"]/div[1]/table[3]/tbody/tr');

    }

    /**
     * Filters currency nodes based on provided search items.
     *
     * @param Crawler $crawlerDom The DOM crawler instance.
     * @param array $searchItems Array of items to search in currency data.
     *
     * @return array An array of Crawler instances that match the search criteria.
     */
    protected function filterSearchItems(Crawler $crawlerDom, array $searchItems): array
    {

        $currenciesNodes = [];

        $crawlerDom->each(function ($node) use ($searchItems, &$currenciesNodes) {

            if ($node->count() === 0) {
                return;
            }

            if ($node->filter('td')->count() !== 5) {
                return;
            }

            $currencyData = $this->extractCurrencyCodeAndNumber($node);

            if ($this->matchesSearchItems($currencyData, $searchItems)) {
                $currenciesNodes[] = $node;
            }

        });

        return $currenciesNodes;

    }

    /**
     * Extracts the basic currency code and number from a currency node.
     *
     * @param Crawler $node The DOM crawler node representing a currency row.
     *
     * @return array An associative array containing 'code' and 'number' of the currency.
     */
    private function extractCurrencyCodeAndNumber(Crawler $node): array
    {
        $tds = $node->filter('td');
        return [
            'code' => $tds->eq(0)->text(),
            'number' => $tds->eq(1)->text()
        ];
    }

    /**
     * Extracts detailed currency data from a currency node.
     *
     * @param Crawler $node The DOM crawler node representing a currency row.
     *
     * @return array An associative array containing detailed currency data including code, number, decimal places, name, and location.
     */
    private function extractCurrencyData(Crawler $node): array
    {
        $tds = $node->filter('td');
        return [
            'code' => $tds->eq(0)->text(),
            'number' => (int) $tds->eq(1)->text(),
            'decimal' => (int) $tds->eq(2)->text(),
            'name' => $tds->eq(3)->text(),
            'locations' => $this->extractLocation($tds->eq(4)),
        ];
    }

    /**
     * Checks if the extracted currency data matches any of the search items.
     *
     * @param array $currencyData The currency data extracted from a node.
     * @param array $searchItems The search items.
     *
     * @return bool True if a match is found, false otherwise.
     */
    private function matchesSearchItems(array $currencyData, array $searchItems): bool
    {
        return in_array($currencyData['code'], $searchItems) || in_array($currencyData['number'], $searchItems);
    }

    /**
     * Extracts location information from a currency node.
     *
     * @param Crawler $node The DOM crawler node.
     *
     * @return array An array of locations associated with the currency.
     */
    private function extractLocation(Crawler $node): array
    {

        $locations = [];
        $lastFlag = null;

        $node->filter('span.mw-image-border, a')->each(function ($children) use (&$locations, &$lastFlag) {
            if (!in_array($children->nodeName(), ['span', 'a'])) {
                return;
            }

            if ($children->nodeName() === 'span') {
                $lastFlag = $this->extractLocationFlag($children);
                return;
            }

            $title = $children->attr('title');

            if ($title) {
                $locations[] = ['name' => $title, 'icon' => $lastFlag];
                $lastFlag = null;
            }

        });

        return $locations;

    }


    /**
     * Extracts and formats the location flag URL from a node.
     *
     * @param Crawler $node The DOM crawler node containing an image.
     *
     * @return string|null The formatted URL of the location flag if available, null otherwise.
     */
    private function extractLocationFlag(Crawler $node): ?string
    {
        $imgSrc = $node->filter('img')->attr('src');
        return str_starts_with($imgSrc, '//') ? 'https:' . $imgSrc : $imgSrc;
    }

}

