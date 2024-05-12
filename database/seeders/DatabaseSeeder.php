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
            Role::create([
                'name' => $r,
            ]);
        }

        User::create([
            'role_id' => 1,
            'fio' => 'Админ',
            'email' => 'admin',
            'password' => bcrypt('tasar232'),
            'mira_id' => 1
        ]);
    }
}
