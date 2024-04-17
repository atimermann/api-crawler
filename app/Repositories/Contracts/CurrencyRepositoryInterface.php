<?php

namespace App\Repositories\Contracts;

interface CurrencyRepositoryInterface
{
    /**
     * Retrieves an array of currencies filtered by specific codes and numbers.
     *
     * @param array $codeAndNumberToSearch Associative array with keys for 'code' and 'number' used for searching.
     *
     * @return array An array representing the currencies that match the search criteria.
     */
    public function getCurrenciesByCodeAndNumber(array $codeAndNumberToSearch): array;

    /**
     * Saves currency data to the repository.
     *
     * @param mixed $item The currency data to be saved. The type and structure depend on the implementation.
     *
     * @return mixed The result of the save operation, which could be a boolean, a model instance, or void, depending on implementation.
     */
    public function save(mixed $item): mixed;
}
