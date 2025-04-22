<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    private const PREFIX_KEY_CODE = 'DMSP-';

    protected $fillable = [
        'name',
        'code',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public static function generateCode($length = 10) {
        do {
            $code = self::PREFIX_KEY_CODE . Str::upper(Str::random($length));
        } while (Category::where('code', $code)->exists());
    
        return $code;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
