<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkoutId',
        'storeId',
        'emailCustomer',
        'amount',
        'currency',
        'callBackUrl',
    ];

    public function checkout(): HasOne
    {
        return $this->hasOne(Checkout::class, 'checkoutId', 'checkoutId');
    }
}
