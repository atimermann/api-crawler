<?php

namespace App\Repositories\Eloquent;

use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepositoryInterface;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    public function findExistingItems(array $searchItems)
    {
        return Currency::whereIn('code', $searchItems)
            ->orWhereIn('number', $searchItems)
            ->get();
    }

    public function save($item)
    {
        // TODO: Implement save() method.
    }
}
