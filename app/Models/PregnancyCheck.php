<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PregnancyCheck extends Model
{
    use HasFactory;

    protected $table = 'pregnancy_checks';

    protected $guarded = ['id'];

    public function familyParents()
    {
        return $this->belongsTo(FamilyParent::class, 'parent_id');
    }

    public function officers()
    {
        return $this->belongsTo(Officer::class, 'officer_id');
    }

    public function vaccines()
    {
        return $this->belongsTo(Vaccine::class, 'vaccine_id');
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'medicine_usages')->withPivot('quantity', 'dosage_instructions', 'meal_time', 'notes')->withTimestamps();
    }

    // Calculate BMI and status
    public function calculateBMI()
    {
        if (!$this->mother_weight || !$this->mother_height) {
            return null;
        }

        $heightInMeters = $this->mother_height / 100;
        $bmi = $this->mother_weight / ($heightInMeters * $heightInMeters);

        return round($bmi, 2);
    }

    public function getBMIStatus($bmi = null)
    {
        if (!$bmi) {
            $bmi = $this->calculateBMI();
        }

        if (!$bmi) return null;

        if ($bmi < 18.5) return 'Kurang Berat Badan';
        if ($bmi >= 18.5 && $bmi < 25) return 'Normal';
        if ($bmi >= 25 && $bmi < 30) return 'Kelebihan Berat Badan';
        if ($bmi >= 30 && $bmi < 40) return 'Obesitas';
        if ($bmi >= 40 && $bmi < 50) return 'Obesitas Parah';
        return 'Obesitas Ekstrem';
    }
}
