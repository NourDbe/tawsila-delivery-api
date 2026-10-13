<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
    ];

    /**
     * Orders assigned to this driver.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
