<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetodistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $role = [
        //     [4, 'Методист1', 'metodist1@mail.ru', 'tasar232'],
        //     [4, 'Методист2', 'metodist2@mail.ru', 'tasar232'], 
        //     [4, 'Методист3', 'metodist3@mail.ru', 'tasar232'],
        //     [4, 'Методист4', 'metodist4@mail.ru', 'tasar232'],
        //     [4, 'Методист5', 'metodist5@mail.ru', 'tasar232']
        // ];
        // foreach($role as $r){
        //     User::create([
        //         'role_id' => $r[0],
        //         'fio' => $r[1],
        //         'email' => $r[2],
        //         'password' => bcrypt($r[3]),
        //         'mira_id' => $r[0]
        //     ]);
        // } 
    }
}
