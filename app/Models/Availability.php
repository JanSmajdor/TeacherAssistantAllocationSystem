<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $table = 'ta_availability';

    protected $fillable = [
        'ta_id',
        'day',
        'start_time',
        'end_time'
    ];

    public function teachingAssistant()
    {
        return $this->belongsTo(TeachingAssistant::class, 'ta_id');
    }
}
