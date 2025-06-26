<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Immunization extends Model
{
    use HasFactory;

    protected $table = 'immunizations';

    protected $guarded = ['id'];

    public function familyChildren()
    {
        return $this->belongsTo(FamilyChildren::class, 'children_id');
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
}
