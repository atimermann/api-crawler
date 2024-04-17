<?php

namespace Tests\Unit;

use App\Services\ScraperService\CurrencyScraperService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DomCrawler\Crawler;

class CurrencyScraperServiceTest extends TestCase
{

    private CurrencyScraperService $service;
    private string $targetHtml;

    /**
     * Initializes the test environment and the CurrencyScraperService instance.
     * Loads a local HTML file to simulate the scraping target.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CurrencyScraperService();


        $pathToHtml = dirname(__DIR__) . '/srapperTarget.html';
        $this->targetHtml = file_get_contents($pathToHtml);
    }

    /**
     * Helper method to invoke private methods
     * @throws ReflectionException
     */
    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Test if the 'extractLocationFlag' method correctly adds 'https:' to relative URLs.
     * Utilizes a data provider for different image source scenarios.
     *
     * @throws ReflectionException
     */
    #[DataProvider('flagUrlDataProvider')]
    public function testExtractLocationFlagAddsHttpsIfNecessary()
    {
        // Criar um exemplo de HTML que o crawler irá processar
        $html = '<div><img src="//example.com/flag.png" alt=""></div>';
        $crawler = new Crawler($html);

        // Método para testar
        $flagUrl = $this->invokeMethod($this->service, 'extractLocationFlag', [$crawler]);

        // Verifica se "https:" é adicionado corretamente
        $this->assertEquals('https://example.com/flag.png', $flagUrl);
    }

    /**
     * Provides sets of HTML strings and expected URL results for the flag URL extraction test.
     *
     * @return array Array of test data for flag URL extraction.
     */
    public static function flagUrlDataProvider(): array
    {
        return [
            ['<div><img src="//example.com/flag.png" alt=""></div>', 'https://example.com/flag.png'],
            ['<div><img src="http://example.com/flag.png" alt=""></div>', 'http://example.com/flag.png'],
        ];
    }

    /**
     * Test if 'extractCurrencyCodeAndNumber' correctly extracts currency code and number from a DOM node.
     *
     * @throws ReflectionException
     */
    public function testExtractCurrencyCodeAndNumber()
    {
        $html = '<tr><td>USD</td><td>840</td></tr>';
        $crawler = new Crawler($html);
        $node = $crawler->filter('tr')->first();

        $result = $this->invokeMethod($this->service, 'extractCurrencyCodeAndNumber', [$node]);

        $expected = ['code' => 'USD', 'number' => '840'];
        $this->assertEquals($expected, $result);
    }

    /**
     * Test if 'matchesSearchItems' accurately checks for the presence of currency data in a search items array.
     *
     * @throws ReflectionException
     */
    public function testMatchesSearchItems()
    {
        $currencyData = ['code' => 'USD', 'number' => '840'];
        $searchItems = ['USD', '978'];

        $result = $this->invokeMethod($this->service, 'matchesSearchItems', [$currencyData, $searchItems]);

        $this->assertTrue($result);
    }


    /**
     * Test if 'extractCurrenciesNodes' correctly extracts currency nodes from the DOM.

     * @throws ReflectionException
     */
    public function testExtractCurrenciesNodes()
    {
        $crawler = new Crawler($this->targetHtml);
        $nodes = $this->invokeMethod($this->service, 'extractCurrenciesNodes', [$crawler]);
        $this->assertEquals(179, $nodes->count());
    }


}
