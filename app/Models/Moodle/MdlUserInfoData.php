<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlUserInfoData extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_user_info_data";
    protected $connection = 'pgsql_moodle';

    protected $hidden = [];

}
