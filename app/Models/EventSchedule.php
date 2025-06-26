<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSchedule extends Model
{
    use HasFactory;

    protected $table = 'event_schedules';

    protected $guarded = ['id'];

    public function officers()
    {
        return $this->belongsTo(Officer::class, 'officer_id');
    }
}
