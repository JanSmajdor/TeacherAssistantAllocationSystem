<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TAAreaOfKnowledge extends Model
{
    protected $table = 'ta_areas_of_knowledge';

    protected $fillable = [
        'ta_id',
        'area_id'
    ];

    public $timestamps = false;

    public function teachingAssistant()
    {
        return $this->belongsTo(TeachingAssistant::class, 'ta_id');
    }

    public function areaOfKnowledge()
    {
        return $this->belongsTo(AreaOfKnowledge::class, 'area_id');
    }
}
