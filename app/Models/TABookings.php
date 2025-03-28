<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TABookings extends Model
{
    protected $table = 'ta_bookings';
    
    protected $fillable = [
        'booking_id',
        'ta_id',
    ];

    public $timestamps = true;
}
