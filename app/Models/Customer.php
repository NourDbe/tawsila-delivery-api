<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
    ];

    /**
     * Orders created by this customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
