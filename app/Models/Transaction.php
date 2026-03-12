<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use \App\Traits\HasUUID;
    use \Wildside\Userstamps\Userstamps;

    protected $guarded = ['id', 'id_hash'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function targetAccount()
    {
        return $this->belongsTo(Account::class, 'target_account_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
