<?php

namespace App\Bussiness\Models;

use Jenssegers\Mongodb\Eloquent\Model;

abstract class RelationshipModel extends Model
{
    protected $primaryKey = null;

    public $incrementing = false;
    public $timestamps = false;

}
