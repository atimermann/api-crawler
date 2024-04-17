<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;


class CurrencyApiTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Sets up the testing environment before each test method.
     * Mocks the CurrencyScraperService and binds it to the service container.
     */
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
//        $this->app->instance(CurrencyScraperService::class, $mockScraper);
    }

    /**
     * Tests the currency fetch endpoint with provided data and expects a successful response.
     * Asserts that the API response matches the expected JSON structure.
     *
     * @param array $requestData The currency codes or numbers to fetch.
     * @param array $expectedResponse The expected response structure.
     *
     */
    #[Test]
    #[DataProvider('currencyDataProvider')]
    public function testFetchCurrenciesEndpoint(array $requestData, array $expectedResponse)
    {

        $response = $this->postJson('/api/currencies', $requestData);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($expectedResponse);
    }

    /**
     * Tests the currency fetch endpoint twice with different data sets.
     * Verifies that the first and second responses match their expected outcomes.
     *
     * @param array $requestData First API request data.
     * @param array $expectedResponse Expected response for the first API call.
     * @param array $secondRequestData Second API request data.
     * @param array $expectedSecondResponse Expected response for the second API call.
     *
     */
    #[Test]
    #[DataProvider('currencyDataProvider')]
    public function testFetchSecondCurrenciesEndpoint(array $requestData, array $expectedResponse, array $secondRequestData, array $expectedSecondResponse)
    {

        $this->postJson('/api/currencies', $requestData);

        $secondResponse = $this->postJson('/api/currencies', $secondRequestData);
        $secondResponse->assertStatus(Response::HTTP_CREATED);
        $secondResponse->assertJson($expectedSecondResponse);

    }

    /**
     * Provides multiple test cases for currency API endpoint tests.
     * Each test case array contains request data and the corresponding expected response.
     *
     * @return array Array of test cases for the data provider.
     */
    static public function currencyDataProvider(): array
    {
        return [
            // =============== BRL
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
                        'fetchFromCrawler' => 0,
                        'fetchFromDatabase' => 0,
                        'fetchFromCache' => 1,
                        'length' => 1
                    ]

                ]
            ],
            // =============== USD, JPY
            [
                ['code_list' => ['NZD', 'OMR']],
                [
                    'data' => [
                        [
                            'code' => 'NZD',
                            'number' => 554,
                            'decimal' => 2,
                            'name' => 'Dólar da Nova Zelândia',
                            'locations' => [
                                [
                                    'name' => 'Nova Zelândia',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Flag_of_New_Zealand.svg/22px-Flag_of_New_Zealand.svg.png'
                                ],
                                [
                                    'name' => 'Ilhas Cook',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/35/Flag_of_the_Cook_Islands.svg/22px-Flag_of_the_Cook_Islands.svg.png'
                                ],
                                [
                                    'name' => 'Niue',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/01/Flag_of_Niue.svg/20px-Flag_of_Niue.svg.png'
                                ],
                                [
                                    'name' => 'Ilhas Pitcairn',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/88/Flag_of_the_Pitcairn_Islands.svg/20px-Flag_of_the_Pitcairn_Islands.svg.png'
                                ],
                                [
                                    'name' => 'Toquelau',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Flag_of_Tokelau.svg/20px-Flag_of_Tokelau.svg.png'
                                ]
                            ]
                        ],
                        [
                            'code' => 'OMR',
                            'number' => 512,
                            'decimal' => 3,
                            'name' => 'Rial Omani',
                            'locations' => [
                                [
                                    'name' => 'Omã',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/dd/Flag_of_Oman.svg/22px-Flag_of_Oman.svg.png'
                                ],
                                [
                                    'name' => 'Omã',
                                    'icon' => null
                                ]
                            ]
                        ]
                    ],
                    'info' => [
                        'fetchFromCrawler' => 2,
                        'fetchFromDatabase' => 0,
                        'fetchFromCache' => 0,
                        'length' => 2
                    ]
                ],
                ["code_list" => ["NZD", "OMR", "RUB"]],
                [
                    'data' => [
                        [
                            'code' => 'NZD',
                            'number' => 554,
                            'decimal' => 2,
                            'name' => 'Dólar da Nova Zelândia',
                            'locations' => [
                                [
                                    'name' => 'Nova Zelândia',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Flag_of_New_Zealand.svg/22px-Flag_of_New_Zealand.svg.png'
                                ],
                                [
                                    'name' => 'Ilhas Cook',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/35/Flag_of_the_Cook_Islands.svg/22px-Flag_of_the_Cook_Islands.svg.png'
                                ],
                                [
                                    'name' => 'Niue',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/01/Flag_of_Niue.svg/20px-Flag_of_Niue.svg.png'
                                ],
                                [
                                    'name' => 'Ilhas Pitcairn',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/88/Flag_of_the_Pitcairn_Islands.svg/20px-Flag_of_the_Pitcairn_Islands.svg.png'
                                ],
                                [
                                    'name' => 'Toquelau',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Flag_of_Tokelau.svg/20px-Flag_of_Tokelau.svg.png'
                                ]
                            ]
                        ],
                        [
                            'code' => 'OMR',
                            'number' => 512,
                            'decimal' => 3,
                            'name' => 'Rial Omani',
                            'locations' => [
                                [
                                    'name' => 'Omã',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/dd/Flag_of_Oman.svg/22px-Flag_of_Oman.svg.png'
                                ],
                                [
                                    'name' => 'Omã',
                                    'icon' => null
                                ]
                            ]
                        ],
                        [
                            'code' => 'RUB',
                            'number' => 643,
                            'decimal' => 2,
                            'name' => 'Rublo',
                            'locations' => [
                                [
                                    'name' => 'Rússia',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Flag_of_Russia.svg/22px-Flag_of_Russia.svg.png'
                                ],
                                [
                                    'name' => 'Abkhazia',
                                    'icon' => null
                                ],
                                [
                                    'name' => 'Ossétia do Sul',
                                    'icon' => null
                                ]
                            ]
                        ]
                    ],
                    'info' => [
                        'fetchFromCrawler' => 1,
                        'fetchFromDatabase' => 0,
                        'fetchFromCache' => 2,
                        'length' => 3
                    ]
                ]
            ],
            // =============== 950, 951
            [
                ["number" => 600],
                [
                    'data' => [
                        [
                            'code' => 'PYG',
                            'number' => 600,
                            'decimal' => 0,
                            'name' => 'Guarani',
                            'locations' => [
                                [
                                    'name' => 'Paraguai',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/Flag_of_Paraguay.svg/22px-Flag_of_Paraguay.svg.png'
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
                ["number" => 690],
                [
                    'data' => [
                        [
                            'code' => 'SCR',
                            'number' => 690,
                            'decimal' => 2,
                            'name' => 'Rupia das Seychelles',
                            'locations' => [
                                [
                                    'name' => 'Seicheles',
                                    'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Flag_of_Seychelles.svg/20px-Flag_of_Seychelles.svg.png'
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
                ]

            ]

        ];
    }

}
