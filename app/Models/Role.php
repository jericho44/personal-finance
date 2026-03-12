<?php

namespace App\Models;

use App\Traits\HasUUID;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Role extends Model
{
    use HasFactory, HasUUID, SoftCascadeTrait, SoftDeletes, Userstamps;

    protected $guarded = ['id', 'id_hash'];

    protected $casts = [
        'created_at' => 'string',
        'updated_at' => 'string',
        'deleted_at' => 'string',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
