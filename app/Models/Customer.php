<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    private const PREFIX_KEY_CODE = 'KH-';

    private const GENDER = [
        0 => 'Nam',
        1 => 'Nữ',
    ];

    private const COLOR_GENDER = [
        0 => 'primary',
        1 => 'danger',
    ];

    protected $fillable = [
        'code',
        'full_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public static function generateCodeCustomer($length = 10) {
        do {
            $code = self::PREFIX_KEY_CODE . Str::upper(Str::random($length));
        } while (Customer::where('code', $code)->exists());
    
        return $code;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGender($gender){
        return in_array($gender, array_keys(self::GENDER)) ? self::GENDER[$gender] : 'X';
    }

    public function getColorGender($gender){
        return in_array($gender, array_keys(self::COLOR_GENDER)) ? self::COLOR_GENDER[$gender] : 'primary';
    }
}
