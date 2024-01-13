<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlUser extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_user";
    protected $connection = 'pgsql_moodle';

    protected $hidden = [
       'auth',
       'confirmed',
       'policyagreed',
       'deleted',
       'suspended',
       'mnethostid',
       'idnumber',
       'emailstop',
       'icq',
       'skype',
       'yahoo',
       'aim',
       'msn',
       'phone1',
       'phone2',
       'institution',
       'department',
       'address',
       'city',
       'country',
       'lang',
       'calendartype',
       'theme',
       'timezone',
       'firstaccess',
       'lastaccess',
       'lastlogin',
       'currentlogin',
       'lastip',
       'secret',
       'picture',
       'url',
       'description',
       'descriptionformat',
       'mailformat',
       'maildigest',
       'maildisplay',
       'autosubscribe',
       'trackforums',
       'timecreated',
       'timemodified',
       'trustbitmask',
       'imagealt',
       'lastnamephonetic',
       'firstnamephonetic',
       'middlename',
       'alternatename',
       'moodlenetprofile'
    ];


    public function getUserEnrole()
    {
        return $this->hasMany('App\Models\Moodle\MdlUserEnrolments', 'userid', 'id');//->with('getEnrole.getCourse');
    }
}
