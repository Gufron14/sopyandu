<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Elderly extends Model
{
    use HasFactory;

    protected $table = 'elderlies';

    protected $guarded = ['id'];

    public static function elderlyCount()
    {
        return self::count();
    }

    public function elderlyChecks()
    {
        return $this->hasMany(ElderlyCheck::class, 'elderly_id');
    }
}
