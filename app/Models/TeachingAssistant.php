<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingAssistant extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'contracted_hours',
        'available_hours',
    ];
}
