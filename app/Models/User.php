<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'role', 'rank',
        'pictures_count', 'orders_count', 'img', 'profile_views', 'banned_until',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'banned_until' => 'datetime',
    ];

    public function pictures()
    {
        return $this->hasMany(Picture::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function purchasedOrders()
    {
        return $this->hasMany(Order::class , 'buyer_id');
    }

    public function soldOrders()
    {
        return $this->hasMany(Order::class , 'seller_id');
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function isAdmin(): bool
    {
        return $this->role == 2;
    }

    public function isBlocked(): bool
    {
        return $this->banned_until && $this->banned_until->isFuture();
    }
}
