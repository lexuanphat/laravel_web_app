<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'contact_phone',
        'user_id',
        'created_at',
        'updated_at',
    ];

    // protected $casts = [
    //     'created_at' => 'datetime:d/m/Y H:i',
    //     'updated_at' => 'datetime:d/m/Y H:i',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
