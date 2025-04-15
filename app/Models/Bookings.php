<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'module_id',
        'module_leader_id',
        'num_tas_requested',
        'date_from',
        'date_to',
        'booking_type',
        'site',
        'request_batch_id',
        'status',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function moduleLeader()
    {
        return $this->belongsTo(User::class, 'module_leader_id');
    }

    public function tas()
    {
        return $this->belongsToMany(User::class, 'booking_request_user', 'booking_request_id', 'user_id');
    }

    public function suggestedTa()
    {
        return $this->hasMany(SuggestedTA::class, 'booking_id');
    }
}
