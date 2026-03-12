<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use \App\Traits\HasUUID;
    use \Wildside\Userstamps\Userstamps;

    protected $guarded = ['id', 'id_hash'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
