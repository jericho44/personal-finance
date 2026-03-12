<?php

namespace App\Models;

use App\Traits\HasUUID;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class UserFirebase extends Model
{
    use HasFactory, HasUUID, SoftCascadeTrait, SoftDeletes, Userstamps;

    protected $guarded = ['id', 'id_hash'];

    protected $hidden = ['auth_token'];

    protected $casts = [
        'created_at' => 'string',
        'updated_at' => 'string',
        'deleted_at' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
