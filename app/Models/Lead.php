<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'marketing_id', 'product_id', 'customer_name', 'customer_phone', 'address', 'last_response'
    ];

    public function marketing()
    {
        return $this->belongsTo(\App\Models\User::class, 'marketing_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}
