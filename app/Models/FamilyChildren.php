<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyChildren extends Model
{
    use HasFactory;

    protected $table = 'family_children';

    protected $guarded = ['id'];

    public static function familyChildrenCount()
    {
        return self::count();
    }

    public function familyParents()
    {
        return $this->belongsTo(FamilyParent::class, 'parent_id');
    }

    public function immunizations()
    {
        return $this->hasMany(Immunization::class, 'children_id');
    }

    public function weighings()
    {
        return $this->hasMany(Weighing::class, 'children_id');
    }

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class, 'children_id');
    }
}
