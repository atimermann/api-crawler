<?php

namespace Tests\Feature;

use App\Repositories\Contracts\CurrencyRepositoryInterface;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ScraperService\CurrencyScraperService;
use Illuminate\Http\Response;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;


class CurrencyApiTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

//        $mockScraper = Mockery::mock(CurrencyScraperService::class);
//        $mockScraper->shouldReceive('fetchCurrenciesByCodeOrNumber')
//            ->once()
//            ->with(['BRL'])
//            ->andReturn([
//                [
//                    'code' => 'BRL',
//                    'number' => '986',
//                    'decimal' => '2',
//                    'name' => 'Real',
//                    'locations' => [
//                        [
//                            'location' => 'Brasil',
//                            'flag' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Flag_of_Brazil.svg/22px-Flag_of_Brazil.svg.png'
//                        ]
//                    ]
//                ]
//            ]);

        // Substituir a instância real pela mockada no service container do Laravel
//        $this->app->instance(CurrencyScraperService::class, $mockScraper);
    }

    #[Test]
    #[DataProvider('currencyDataProvider')]
    public function testFetchCurrenciesEndpoint($requestData, $expectedResponse)
    {

        $response = $this->postJson('/api/currencies', $requestData);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($expectedResponse);
    }

    #[Test]
    #[DataProvider('currencyDataProvider')]
    public function testFetchSecondCurrenciesEndpoint($requestData, $expectedResponse, $expectedSecondResponse)
    {

        $firstResponse = $this->postJson('/api/currencies', $requestData);
        $firstResponse->assertStatus(Response::HTTP_CREATED);
        $firstResponse->assertJson($expectedResponse);

        $secondResponse = $this->postJson('/api/currencies', $requestData);
        $secondResponse->assertStatus(Response::HTTP_CREATED);
        $secondResponse->assertJson($expectedSecondResponse);

    }

    static public function currencyDataProvider(): array
    {
        return [
            [
                ['code_list' => ['BRL']],
                [
                    'data' => [
                        [
                            'code' => 'BRL',
                            'number' => 986,
                            'decimal' => 2,
                            'name' => 'Real',
                            'locations' => [
                                [
                                    'name' => 'Brasil',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Flag_of_Brazil.svg/22px-Flag_of_Brazil.svg.png'
                                ]
                            ]
                        ]
                    ],
                    'info' => [
                        'fetchFromCrawler' => 1,
                        'fetchFromDatabase' => 0,
                        'fetchFromCache' => 0,
                        'length' => 1
                    ]
                ],
                [
                    'data' => [
                        [
                            'code' => 'BRL',
                            'number' => 986,
                            'decimal' => 2,
                            'name' => 'Real',
                            'locations' => [
                                [
                                    'name' => 'Brasil',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Flag_of_Brazil.svg/22px-Flag_of_Brazil.svg.png'
                                ]
                            ]
                        ]
                    ],
                    'info' => [
                        'fetchFromCrawler' => 0,
                        'fetchFromDatabase' => 1,
                        'fetchFromCache' => 0,
                        'length' => 1
                    ]

                ]
            ]
        ];
    }

}
