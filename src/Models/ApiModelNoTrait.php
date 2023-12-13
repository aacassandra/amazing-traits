<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiModelNoTrait extends Model
{
    use HasFactory;

    protected $table = null;
    protected $guarded = ['id','created_at','updated_at'];
}
