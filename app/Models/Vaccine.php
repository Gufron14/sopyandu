<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    use HasFactory;

    protected $table = 'vaccines';

    protected $guarded = ['id'];

    public function immunizations()
    {
        return $this->hasMany(Immunization::class, 'vaccine_id');
    }

    public function pregnancyChecks()
    {
        return $this->hasMany(PregnancyCheck::class, 'vaccine_id');
    }

    // Scope untuk filter berdasarkan jenis vaksin
    public function scopeByType($query, $type)
    {
        return $query->where('vaccine_type', $type);
    }
}
