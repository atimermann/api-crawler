<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class CurrencyController extends Controller
{

    public function fetchCurrencies(Request $request): JsonResponse
    {

        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request('GET', 'https://pt.wikipedia.org/wiki/ISO_4217');

        $searchItems = ['AED', '238']; // Lista de códigos ou números para buscar

        $results = [];

        // Iterar sobre cada linha da tabela
        $crawler
            ->filterXPath('//*[@id="mw-content-text"]/div[1]/table[3]/tbody/tr')
            ->each(function ($node, $i) use ($searchItems, &$results) {
                if ($node->count() > 0) {
                    $tds = $node->filter('td');
                    if ($tds->count() === 5) {

                        // Extrair os dados das células
                        $code = $tds->eq(0)->text();
                        $number = $tds->eq(1)->text();

                        echo "Analisando {$code}/{$number}...<br>";

                        if (!in_array($code, $searchItems) && !in_array($number, $searchItems)) {
                            echo "Não encontrado...<br>";
                            return;
                        }
                        echo "Encontrado!<br>";


                        $decimal = $tds->eq(2)->text();
                        $name = $tds->eq(3)->text();

                        $location = $tds->eq(4);

                        echo "<pre>" . $location->html() . "</pre>";

                        $location->filter('span.mw-image-border, a')->each(function ($children) {

                            if ($children->nodeName() === 'span') {
                                $imgSrc = $children->filter('img')->attr('src');
                                $imgSrc = strpos($imgSrc, '//') === 0 ? 'https:' . $imgSrc : $imgSrc;
                                echo 'ICON=' . $imgSrc . '<Br>';
                            } elseif ($children->nodeName() === 'a') {
                                $locationUrl = $children->attr('title');
                                echo 'LOCATION=' . $locationUrl . '<Br>';

                            }

                        });
                        echo "<hr>";


                        // Verificar se o código ou número está na lista de busca
                        $results[] = [
                            'code' => $code,
                            'number' => $number,
                            'decimal' => $decimal,
                            'currency_name' => $name,
//                            'country' => $country,
//                            'flag_url' => $flagUrl
                        ];
                    }
                }
            });

        return response()->json('ok', Response::HTTP_ACCEPTED);
    }
}
