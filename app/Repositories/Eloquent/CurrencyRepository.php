<?php

namespace App\Repositories\Eloquent;

use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepositoryInterface;

use Exception;
use Illuminate\Support\Facades\DB;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * Retrieves an array of currencies filtered by codes and numbers.
     *
     * @param array $codeAndNumberToSearch Associative array with keys 'code' and 'number'.
     *
     * @return array An array of currencies along with their associated locations.
     */
    public function getCurrenciesByCodeAndNumber(array $codeAndNumberToSearch): array
    {
        return Currency::with(['locations' => function ($query) {
            $query->select('currency_id', 'name', 'icon');
        }])
            ->select('id', 'code', 'number', 'decimal', 'name')
            ->where(function ($query) use ($codeAndNumberToSearch) {
                foreach ($codeAndNumberToSearch as $item) {
                    $query->orWhere('number', (int) $item)
                        ->orWhere('code', $item);
                }
            })
            ->get()
            ->toArray();

    }


    /**
     * Saves a batch of currency data extracted from scrapping items.
     *
     * Transactions are used to ensure all or none of the currencies are saved,
     * which includes their respective locations.
     *
     * @param array $crawledCurrencies An array of scrapped currency data.
     *
     * @throws Exception If an error occurs during the database transaction.
     */
    public function saveCurrencies(array $crawledCurrencies): void
    {

        if (empty($crawledCurrencies)) {
            return;
        }


        DB::beginTransaction();
        try {

            foreach ($crawledCurrencies as $item) {
                $this->save($item);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }

    }

    /**
     * Saves a single currency and its locations to the database.
     *
     * @param mixed $item The currency data to save.
     * @return Currency The saved Currency model instance.
     */
    public function save(mixed $item): Currency
    {

        $currency = Currency::create([
            'code' => $item['code'],
            'number' => $item['number'],
            'decimal' => $item['decimal'],
            'name' => $item['name']
        ]);


        foreach ($item['locations'] as $locationData) {
            $currency->locations()->create([
                'name' => $locationData['name'],
                'icon' => $locationData['icon']
            ]);

        }

        return $currency;
    }


}
