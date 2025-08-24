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
}
