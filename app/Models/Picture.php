<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $fillable = [
        'user_id', 'img', 'width', 'height', 'name',
        'technique', 'year', 'description',
        'genre_id', 'style_id', 'era_id', 'price', 'listing_type',
        'auction_start_price', 'auction_current_price', 'auction_min_step',
        'auction_buyout_price', 'auction_starts_at', 'auction_ends_at',
        'status',
    ];

    protected $casts = [
        'auction_starts_at' => 'datetime',
        'auction_ends_at' => 'datetime',
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

    public function auctionBids()
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function latestAuctionBid()
    {
        return $this->hasOne(AuctionBid::class)->latestOfMany();
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
