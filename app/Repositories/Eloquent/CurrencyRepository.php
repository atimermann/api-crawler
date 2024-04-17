<?php

namespace App\Repositories\Eloquent;

use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepositoryInterface;

use Illuminate\Support\Facades\DB;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    public function getCurrenciesByCodeAndNumber(array $codeAndNumberToSearch)
    {
        return Currency::with(['locations' => function ($query) {
            $query->select('currency_id', 'name', 'icon');
        }])
            ->select('id', 'code', 'number', 'decimal', 'name')  // Inclua 'id' para mapear corretamente os relacionamentos
            ->get()
            ->toArray();

    }


    public function saveCurrencies($scrappingItems)
    {

        DB::beginTransaction();
        try {

            foreach ($scrappingItems as $item) {
                $this->save($item);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

    }

    public function save($item)
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
