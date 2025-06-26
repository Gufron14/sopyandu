<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $table = 'medicines';

    protected $guarded = ['id'];

    public function elderlyChecks()
    {
        return $this->belongsToMany(ElderlyCheck::class, 'elderly_check_id')->withPivot('quantity', 'dosage_instructions', 'meal_time', 'notes')->withTimestamps();
    }

    public function pregnancyChecks()
    {
        return $this->belongsToMany(PregnancyCheck::class, 'pregnancy_check_id')->withPivot('quantity', 'dosage_instructions', 'meal_time', 'notes')->withTimestamps();
    }

    public function immunizations()
    {
        return $this->belongsToMany(Immunization::class, 'immunization_id')->withPivot('quantity', 'dosage_instructions', 'meal_time', 'notes')->withTimestamps();
    }
}
