<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'buyer_id', 'seller_id', 'picture_id', 'price',
        'pickup_point', 'recipient_name', 'unique_code',
        'status', 'payment_id', 'payment_status',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class , 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class , 'seller_id');
    }

    public function picture()
    {
        return $this->belongsTo(Picture::class);
    }
}
