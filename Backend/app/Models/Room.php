<?php

namespace App\Models;

use App\Models\Code;
use App\Events\RefreshEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'completed', "uuid"];

    protected $cast = [
        "completed" => "boolean",
    ];

    public function mates() {
        return $this->belongsToMany(Mate::class);
    }

    public function codes() {
        return $this->belongsToMany(Code::class);
    }

    public function check($codeToCheck) {
        $normalizedToCheck = strtolower(str_replace(" ", "", $codeToCheck));

        $codeModel = $this->codes()->whereRaw("LOWER(REPLACE(code, ' ', '')) = ?", [$normalizedToCheck])->first();

        if ($codeModel) {
            $codeModel->founded = true;
            $codeModel->save();

            event(new RefreshEvent("refresh"));
        }
    }

    public function calculateFoundPercentage() {
        $totalCodes = $this->codes->count();
        if ($totalCodes === 0) {
            return 0.0;
        }

        $foundCodes = $this->codes->where('founded', true)->count();

        $percentage = ($foundCodes / $totalCodes) * 100;

        return round($percentage, 2);
    }
}
