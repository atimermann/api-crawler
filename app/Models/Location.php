<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['currency_id', 'name', 'icon'];

    /**
     * Get the currency that owns the location.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
