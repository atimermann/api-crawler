<?php

namespace App\Repositories\Contracts;

interface CurrencyRepositoryInterface
{
    public function getCurrenciesByCodeAndNumber(array $codeAndNumberToSearch);

    public function save($item);
}
