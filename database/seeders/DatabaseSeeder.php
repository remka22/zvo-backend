<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $role = ['Администратор', 'Студент', 'Преподаватель', 'Методист', 'Директор'];
        foreach ($role as $r) {
            if(!Role::where('name', $r)->first())
            Role::create([
                'name' => $r,
            ]);
        }
        if(!User::where('email', 'admin')->first())
        User::create([
            'role_id' => 1,
            'fio' => 'Админ',
            'email' => 'admin',
            'password' => bcrypt('admin'),
            'mira_id' => 1
        ]);
    }
}
