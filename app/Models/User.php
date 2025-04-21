<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // check if the user is an admin
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    // check if the user is a module leader
    public function isModuleLeader(): bool
    {
        return $this->role === 'Module Leader';
    }

    // check if the user is a teaching assistant
    public function isTA(): bool
    {
        return $this->role === 'Teaching Assistant';
    }

    public function teachingAssistant()
    {
        return $this->hasOne(TeachingAssistant::class, 'user_id');
    }
}
