<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'modules';

    protected $fillable = [
        'module_leader_id',
        'module_name',
        'module_code',
        'num_of_students',
    ];

    //when moduleLeader Model gets created, add a belongsToOne relationship here

    public function areasOfKnowledge()
    {
        return $this->belongsToMany(AreaOfKnowledge::class, 'module_areas_of_knowledge', 'module_id', 'area_id');
    }
}
