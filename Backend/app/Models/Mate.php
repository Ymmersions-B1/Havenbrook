<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mate extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function getRoom()
    {
        return $this->belongsToMany(Room::class)->first();
    }
}
