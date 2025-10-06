<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'fee',
        'created_user_id',
        'updated_user_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:d/m/Y',
        'updated_at' => 'datetime:d/m/Y',
    ];
}
