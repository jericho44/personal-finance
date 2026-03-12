<?php

namespace App\Models;

use App\Traits\HasUUID;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class AppVersion extends Model
{
    use HasFactory, HasUUID, SoftCascadeTrait, SoftDeletes, Userstamps;

    protected $guarded = [
        'id',
        'id_hash',
    ];

    protected $casts = [
        'created_at' => 'string',
        'updated_at' => 'string',
        'deleted_at' => 'string',
    ];

    protected $softCascade = [];

    public function getAppTypeNameAttribute()
    {
        $value = null;

        switch ($this->app_type) {
            case 'web':
                $value = 'Website';
                break;

            case 'android':
                $value = 'Android';
                break;

            case 'ios':
                $value = 'IOS';
                break;

            default:
                // code...
                break;
        }

        return $value;
    }
}
