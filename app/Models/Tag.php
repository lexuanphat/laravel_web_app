<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    const TAG_IS = [
        'PRODUCT' => 1,
        'CUSTOMER' => 2,
    ];

    protected $fillable = [
        'tag_name',
        'user_id',
        'type',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function baseQuery(){
        return Tag::orderBy('tag_name', 'asc');
    }

    public function getAllTag() {
        return self::baseQuery()->select("tag_name", "id")->get();
    }
}
