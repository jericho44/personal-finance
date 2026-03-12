<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUUID;
use Wildside\Userstamps\Userstamps;

class Category extends Model
{
    use HasUUID;
    use Userstamps;

    protected $guarded = ['id', 'id_hash'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
