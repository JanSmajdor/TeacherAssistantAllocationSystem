<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModuleAreasOfKnowledge extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'module_areas_of_knowledge';

    protected $fillable = [
        'module_id',
        'area_id',
    ];

    //when moduleLeader Model gets created, add a belongsToOne relationship here
}
