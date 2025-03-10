<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{

    protected $table = 'ta_availability';

    protected $fillable = [
        'ta_id',
        'available_from',
        'available_to'
    ];

    public $timestamps = true;

    public function teachingAssistant()
    {
        return $this->belongsTo(TeachingAssistant::class, 'user_id');
    }
}
