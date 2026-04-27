<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    protected $fillable = ['name'];

    public function pictures()
    {
        return $this->hasMany(Picture::class);
    }
}
