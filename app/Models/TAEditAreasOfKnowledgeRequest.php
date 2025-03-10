<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TAEditAreasOfKnowledgeRequest extends Model
{
    protected $table = 'ta_edit_areas_of_knowledge_request';

    protected $fillable = [
        'ta_id', 
        'area_id', 
        'request_status'
    ];

    public $timestamps = true;
    
    public function teaching_assistant()
    {
        return $this->belongsTo(TeachingAssistant::class, 'ta_id');
    }
    public function area_of_knowledge()
    {
        return $this->belongsTo(AreaOfKnowledge::class, 'area_id');
    }
}
