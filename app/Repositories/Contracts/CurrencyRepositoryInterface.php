<?php

namespace App\Repositories\Contracts;

interface CurrencyRepositoryInterface
{
    public function findExistingItems(array $searchItems);

    public function save($item);
}
