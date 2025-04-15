<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuggestedTA extends Model
{
    protected $table = 'suggested_tas_booking';

    protected $fillable = [
        'booking_id',
        'ta_id',
    ];
    
    public $timestamps = true;

    public function ta()
    {
        return $this->belongsTo(TeachingAssistant::class, 'ta_id');
    }
}
