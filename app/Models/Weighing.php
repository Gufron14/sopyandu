<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Weighing extends Model
{
    use HasFactory;

    protected $table = 'weighings';

    protected $guarded = ['id'];

    // Relational methods
    public function familyChildren()
    {
        return $this->belongsTo(FamilyChildren::class, 'children_id');
    }

    public function officers()
    {
        return $this->belongsTo(Officer::class, 'officer_id');
    }

    // Anthropometric Z-Score calculation methods
    public function calculateZScore($value, $median, $sd)
    {
        return ($value - $median) / $sd;
    }

    // Get nutrition status based on Z-Score
    public function getNutritionStatus($zScore)
    {
        if ($zScore < -3) return 'Buruk';  // Severe
        if ($zScore < -2) return 'Kurang'; // Moderate
        if ($zScore <= 2) return 'Baik';   // Normal
        return 'Lebih';                     // Excess
    }

    // Calculate Weight-for-Age (BB/U)
    public function calculateWeightForAge()
    {
        $child = $this->familyChildren;
        $ageInMonths = $this->getAgeInMonths();
        $gender = $child->gender;
        
        // Get median and SD values from WHO standards
        $standard = $this->getWHOStandard('weight_for_age', $gender, $ageInMonths);
        
        if (!$standard) return null;
        
        $zScore = $this->calculateZScore(
            $this->weight,
            $standard['median'],
            $standard['sd']
        );
        
        return [
            'z_score' => round($zScore, 2),
            'status' => $this->getNutritionStatus($zScore)
        ];
    }

    // Calculate Height-for-Age (TB/U)
    public function calculateHeightForAge()
    {
        $child = $this->familyChildren;
        $ageInMonths = $this->getAgeInMonths();
        $gender = $child->gender;
        
        $standard = $this->getWHOStandard('height_for_age', $gender, $ageInMonths);
        
        if (!$standard) return null;
        
        $zScore = $this->calculateZScore(
            $this->height,
            $standard['median'],
            $standard['sd']
        );
        
        return [
            'z_score' => round($zScore, 2),
            'status' => $this->getNutritionStatus($zScore)
        ];
    }

    // Calculate Weight-for-Height (BB/TB)
    public function calculateWeightForHeight()
    {
        $child = $this->familyChildren;
        $gender = $child->gender;
        $heightCm = round($this->height, 1);
        
        $standard = $this->getWHOStandard('weight_for_height', $gender, $heightCm);
        
        if (!$standard) return null;
        
        $zScore = $this->calculateZScore(
            $this->weight,
            $standard['median'],
            $standard['sd']
        );
        
        return [
            'z_score' => round($zScore, 2),
            'status' => $this->getNutritionStatus($zScore)
        ];
    }

    // Calculate BMI-for-Age (IMT/U)
    public function calculateBMIForAge()
    {
        if (!$this->weight || !$this->height) return null;
        
        $child = $this->familyChildren;
        $ageInMonths = $this->getAgeInMonths();
        $gender = $child->gender;
        
        // Calculate BMI
        $heightInMeters = $this->height / 100;
        $bmi = $this->weight / ($heightInMeters * $heightInMeters);
        
        $standard = $this->getWHOStandard('bmi_for_age', $gender, $ageInMonths);
        
        if (!$standard) return null;
        
        $zScore = $this->calculateZScore(
            $bmi,
            $standard['median'],
            $standard['sd']
        );
        
        return [
            'bmi' => round($bmi, 2),
            'z_score' => round($zScore, 2),
            'status' => $this->getNutritionStatus($zScore)
        ];
    }

    // Helper method to get age in months at weighing date
    protected function getAgeInMonths()
    {
        $child = $this->familyChildren;
        $birthDate = Carbon::parse($child->date_of_birth);
        $weighingDate = Carbon::parse($this->weighing_date);
        
        return $birthDate->diffInMonths($weighingDate);
    }

    // Helper method to get WHO anthropometric standards
    protected function getWHOStandard($type, $gender, $value)
    {
        // Simplified WHO standards - in production, use database table
        $standards = $this->getWHOStandardsData();
        
        $key = $type . '_' . strtolower($gender);
        
        if (!isset($standards[$key])) return null;
        
        $data = $standards[$key];
        
        // Find closest value
        $closestKey = null;
        $minDiff = PHP_FLOAT_MAX;
        
        foreach ($data as $standardValue => $stats) {
            $diff = abs($standardValue - $value);
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closestKey = $standardValue;
            }
        }
        
        return $closestKey ? $data[$closestKey] : null;
    }

    // WHO Standards Data (simplified version)
    private function getWHOStandardsData()
    {
        return [
            // Weight for Age - Boys (months 0-60)
            'weight_for_age_l' => [
                0 => ['median' => 3.3, 'sd' => 0.4],
                1 => ['median' => 4.5, 'sd' => 0.5],
                2 => ['median' => 5.6, 'sd' => 0.6],
                3 => ['median' => 6.4, 'sd' => 0.7],
                6 => ['median' => 7.9, 'sd' => 0.8],
                12 => ['median' => 9.6, 'sd' => 1.0],
                24 => ['median' => 12.2, 'sd' => 1.3],
                36 => ['median' => 14.3, 'sd' => 1.5],
                48 => ['median' => 16.3, 'sd' => 1.7],
                60 => ['median' => 18.3, 'sd' => 1.9],
            ],
            // Weight for Age - Girls (months 0-60)
            'weight_for_age_p' => [
                0 => ['median' => 3.2, 'sd' => 0.4],
                1 => ['median' => 4.2, 'sd' => 0.5],
                2 => ['median' => 5.1, 'sd' => 0.6],
                3 => ['median' => 5.8, 'sd' => 0.7],
                6 => ['median' => 7.3, 'sd' => 0.8],
                12 => ['median' => 8.9, 'sd' => 1.0],
                24 => ['median' => 11.5, 'sd' => 1.3],
                36 => ['median' => 13.9, 'sd' => 1.5],
                48 => ['median' => 15.9, 'sd' => 1.7],
                60 => ['median' => 17.9, 'sd' => 1.9],
            ],
            // Height for Age - Boys (months 0-60)
            'height_for_age_l' => [
                0 => ['median' => 49.9, 'sd' => 1.9],
                1 => ['median' => 54.7, 'sd' => 2.0],
                2 => ['median' => 58.4, 'sd' => 2.1],
                3 => ['median' => 61.4, 'sd' => 2.2],
                6 => ['median' => 67.6, 'sd' => 2.4],
                12 => ['median' => 75.7, 'sd' => 2.6],
                24 => ['median' => 87.1, 'sd' => 3.0],
                36 => ['median' => 96.1, 'sd' => 3.3],
                48 => ['median' => 103.3, 'sd' => 3.6],
                60 => ['median' => 110.0, 'sd' => 3.9],
            ],
            // Height for Age - Girls (months 0-60)
            'height_for_age_p' => [
                0 => ['median' => 49.1, 'sd' => 1.9],
                1 => ['median' => 53.7, 'sd' => 2.0],
                2 => ['median' => 57.1, 'sd' => 2.1],
                3 => ['median' => 59.8, 'sd' => 2.2],
                6 => ['median' => 65.7, 'sd' => 2.4],
                12 => ['median' => 74.0, 'sd' => 2.6],
                24 => ['median' => 85.7, 'sd' => 3.0],
                36 => ['median' => 95.1, 'sd' => 3.3],
                48 => ['median' => 102.7, 'sd' => 3.6],
                60 => ['median' => 109.4, 'sd' => 3.9],
            ],
        ];
    }

    // Calculate all anthropometric measurements
    public function calculateAllMeasurements()
    {
        return [
            'bb_u' => $this->calculateWeightForAge(),
            'tb_u' => $this->calculateHeightForAge(),
            'bb_tb' => $this->calculateWeightForHeight(),
            'imt_u' => $this->calculateBMIForAge()
        ];
    }
}
