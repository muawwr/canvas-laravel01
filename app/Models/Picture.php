<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $fillable = [
        'user_id', 'img', 'width', 'height', 'name',
        'technique', 'year', 'description',
        'genre_id', 'style_id', 'era_id', 'price', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function era()
    {
        return $this->belongsTo(Era::class);
    }

    public function cartEntries()
    {
        return $this->hasMany(Cart::class);
    }

    public function favoriteEntries()
    {
        return $this->hasMany(Favorite::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isSold(): bool
    {
        return $this->orders()->where('payment_status', 'succeeded')->exists();
    }

    public function soldCount(): int
    {
        return $this->orders()->where('payment_status', 'succeeded')->count();
    }

    public function likesCount(): int
    {
        return $this->favoriteEntries()->count();
    }
}
