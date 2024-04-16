<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;


    protected $fillable = ['code', 'number', 'decimal', 'name', 'location'];

    /**
     * Get the locations associated with the currency.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}
