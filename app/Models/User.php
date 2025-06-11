<?php

namespace App\Models;

use Attribute;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_TEXT = [
        'admin' => 'Quản trị viên',
        'manage_sale' => 'Quản lý bán hàng',
        'manage_producttion' => 'Quản lý sản xuất',
        'staff_sale' => 'Nhân viên bán hàng',
        'staff_producttion' => 'Nhân viên sản xuất',
    ];

    public const ROLE_ACCESS_PAGE = [
        'admin' => 'admin',
        'manage_sale' => 'manage_sale',
        'manage_producttion' => 'manage_producttion',
    ];

    public const ROLE_VALUE = [
        'admin' => 'admin',
        'manage_sale' => 'manage_sale',
        'manage_producttion' => 'manage_producttion',
        'staff_sale' => 'staff_sale',
        'staff_producttion' => 'staff_producttion',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'role',
        'create_user_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // protected $appends = ['store_name'];

    // public function getStoreNameAttribute()
    // {
    //     return $this->store->name ?? null;
    // }

    public function getRoleNameAttribute()
    {
        return self::ROLE_TEXT[$this->role];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
