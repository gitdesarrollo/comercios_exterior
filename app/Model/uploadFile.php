<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class uploadFile extends Model
{
    protected $fillable = ['file','evento_id','file_name'];
}
