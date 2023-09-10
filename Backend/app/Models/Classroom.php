<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function rooms() {
        return $this->belongsToMany(Room::class);
    }

    public function calculateOverallFoundPercentage() {
        $totalFoundPercentage = 0;
        $roomCount = $this->rooms->count();

        if ($roomCount === 0) {
            return 0.0;
        }

        foreach ($this->rooms as $room) {
            $totalFoundPercentage += $room->calculateFoundPercentage();
        }

        return round($totalFoundPercentage / $roomCount, 2);
    }
}
