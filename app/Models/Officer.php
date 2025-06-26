<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    use HasFactory;

    protected $table = 'officers';

    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class, 'officer_id');
    }

    public function elderlyChecks()
    {
        return $this->hasMany(ElderlyCheck::class, 'elderly_id');
    }

    public function pregnancyChecks()
    {
        return $this->hasMany(PregnancyCheck::class, 'parent_id');
    }

    public function immunizations()
    {
        return $this->hasMany(Immunization::class, 'immunization_id');
    }

    public function weighings()
    {
        return $this->hasMany(Weighing::class, 'weighing_id');
    }

    public function eventSchedules()
    {
        return $this->hasMany(EventSchedule::class, 'officer_id');
    }
}
