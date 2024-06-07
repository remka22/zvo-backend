<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fio',
        'email',
        'password',
        'group_id',
        'role_id',
        'mira_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
    
    public function hasRole($check)
    {
        return $check == $this->role->name;
    
    }

    public function groups(){
        return $this->hasMany('App\Models\Group', 'metodist_id', 'id');
    }
    public function getMetodists(){
        return $this->belongsToMany('App\Models\Role', 'id', 'role_id')->where('role_id', 4);
    }
    public function getTeacherSubjects(){
        return $this->hasMany('App\Models\SubjectTeacher', 'teacher_id', 'id');
    }
    public function getStudent(){
        return $this->hasOne('App\Models\Sudent', 'user_id', 'id');
    }

    public function new_messages(){
        return $this->hasMany('App\Models\Notification', 'user_send_id', 'id')->where('is_read', false);
    }

    // for metodist post
    public function getMetodistGroup($group_id){
        return $this->hasOne('App\Models\Group', 'metodist_id', 'id')->where('id', $group_id);
    }

}
