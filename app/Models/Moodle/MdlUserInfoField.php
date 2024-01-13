<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlUserInfoField extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_user_info_field";
    protected $connection = 'pgsql_moodle';

    protected $hidden = [];

    public function getKafedra($userid)
    {
        return $this->hasOne('App\Models\Moodle\MdlUserInfoData', 'fieldid', 'id')->where('userid', $userid);
    }
}
