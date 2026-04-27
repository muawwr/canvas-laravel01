<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $table = 'favorites';
    public $timestamps = false;

    protected $fillable = ['user_id', 'picture_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function picture()
    {
        return $this->belongsTo(Picture::class);
    }
}
