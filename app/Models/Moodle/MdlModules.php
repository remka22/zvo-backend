<?php

namespace App\Models\Moodle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdlModules extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mdl_modules";
    protected $connection = 'pgsql_moodle';
    protected $hidden = [
        'cron',
        'lastcron',
        'search',
        'visible',
    ];
}
