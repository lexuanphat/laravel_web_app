<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'phone',
        'role',
        'user_id',
        'created_at',
        'updated_at',
    ];

    const ROLE = [
        'CHANH_XE' => 'CHANH_XE',
        'SHIPPER' => 'SHIPPER',
    ];

    const ROLE_RENDER_BLADE = [
        'CHANH_XE' => 'Chành xe',
        'SHIPPER' => 'Shipper',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
