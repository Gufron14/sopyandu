<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElderlyCheck extends Model
{
    use HasFactory;

    protected $table = 'elderly_checks';

    protected $guarded = ['id'];

    public function elderlies()
    {
        return $this->belongsTo(Elderly::class, 'elderly_id');
    }

    public function officers()
    {
        return $this->belongsTo(Officer::class, 'officer_id');
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'medicine_usages')->withPivot('quantity', 'dosage_instructions', 'meal_time', 'notes')->withTimestamps();
    }
}
