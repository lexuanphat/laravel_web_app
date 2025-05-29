<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    private const PREFIX_KEY_CODE = 'SP-';

    protected $fillable = [
       'name',
       'code',
       'sku',
       'price',
       'desc',
       'image_url',
       'category_id',
       'user_id',
       'created_at',
       'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productStock()
    {
        return $this->hasOne(ProductStock::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function generateCode($length = 10) {
        do {
            $code = self::PREFIX_KEY_CODE . Str::upper(Str::random($length));
        } while (Product::where('code', $code)->exists());
    
        return $code;
    }

    protected static function booted() {
        static::deleted(function($product){
            Storage::disk('public')->delete($product->image_url);
        });
    }
}
