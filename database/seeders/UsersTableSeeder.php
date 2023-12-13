<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = [
            [1, 'Админ', 'admin@mail.ru', 123],
            [2, 'Студент', 'student@mail.ru', 123], 
            [3, 'Преподаватель', 'teacher@mail.ru', 123],
            [4, 'Методист', 'metodist@mail.ru', 123],
            [5, 'Директор', 'director@mail.ru', 123]
        ];
        foreach($role as $r){
            User::create([
                'role_id' => $r[0],
                'fio' => $r[1],
                'email' => $r[2],
                'password' => bcrypt($r[3])
            ]);
        } 
    }
}
