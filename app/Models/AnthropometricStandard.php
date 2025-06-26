<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnthropometricStandard extends Model
{
    use HasFactory;

    protected $table = 'anthropometric_standards';

    protected $fillable = [
        'type',      // BB/U, TB/U, BB/TB, IMT/U
        'gender',    // L, P
        'value',     // Age in months for BB/U, TB/U, IMT/U; Height in cm for BB/TB
        'minus_3sd', // -3 SD value
        'minus_2sd', // -2 SD value
        'median',    // Median value
        'plus_2sd',  // +2 SD value
        'plus_3sd'   // +3 SD value
    ];

    // Get the appropriate SD value based on z-score
    public function getSD($score)
    {
        if ($score <= -3) return $this->minus_3sd;
        if ($score <= -2) return $this->minus_2sd;
        if ($score <= 0) return $this->median;
        if ($score <= 2) return $this->plus_2sd;
        return $this->plus_3sd;
    }
}
