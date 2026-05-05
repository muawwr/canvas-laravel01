<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionBid extends Model
{
    protected $fillable = [
        'picture_id',
        'user_id',
        'amount',
    ];

    public function picture()
    {
        return $this->belongsTo(Picture::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
