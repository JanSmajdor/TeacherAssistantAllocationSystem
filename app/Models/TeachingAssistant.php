<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeachingAssistant extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'teaching_assistants';

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function areasOfKnowledge()
    {
        $this->belongsToMany(AreaOfKnowledge::class, 'ta_area_of_knowledge', 'ta_id', 'area_id');
    }

    public function isProfileComplete()
    {
        return !empty($this->contracted_hours) && !empty($this->available_hours) && $this->areasOfKnowledge()->exists();
    }
}
