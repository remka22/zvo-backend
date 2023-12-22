<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DirectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = [
            [5, 'Директор', 'director@mail.ru', 'tasar232!']
        ];
        foreach($role as $r){
            User::create([
                'role_id' => $r[0],
                'fio' => $r[1],
                'email' => $r[2],
                'password' => bcrypt($r[3]),
                'mira_id' => $r[0]
            ]);
        } 
    }
}
