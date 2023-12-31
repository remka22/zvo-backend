<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = ['Администратор', 'Студент', 'Преподаватель', 'Методист', 'Директор'];
        foreach($role as $r){
            Role::create([
                'name' => $r,
            ]);
        } 
    }
}
