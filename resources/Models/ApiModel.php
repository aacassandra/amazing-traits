<?php

namespace App\Models;

use AmazingTraits\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiModel extends Model
{
    use HasFactory, ModelTrait, SoftDeletes;

    protected $table = null;
    protected $guarded = ['id','created_at','updated_at'];
}
