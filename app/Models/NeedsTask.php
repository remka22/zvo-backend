<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeedsTask extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "course_task";
}
