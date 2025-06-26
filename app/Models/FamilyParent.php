<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyParent extends Model
{
    use HasFactory;

    protected $table = 'family_parents';

    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function familyChildren()
    {
        return $this->hasMany(FamilyChildren::class, 'parent_id');
    }

    public function pregnancyChecks()
    {
        return $this->hasMany(PregnancyCheck::class, 'parent_id');
    }

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class, 'parent_id');
    }

    // Scope untuk ibu hamil (asumsi ada field is_pregnant)
    public function scopePregnant($query)
    {
        return $query->where('is_pregnant', true);
    }
}
