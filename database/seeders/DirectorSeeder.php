<?php

namespace Database\Seeders;

use App\Models\Campus;
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
        // $director = Campus::create([
        //     'miraid' => null,
        //     'last_name' => 'директора',
        //     'first_name' => 'Фио',
        //     'nomz' => 'd1',
        //     'cohort'  => 'Преподаватель',
        //     'subfaculty' => 'ЗВО',
        //     'faculty' => 'ЗВО',
        //     'login' => 'd1',
        // ]);
        // $l = $director->last_name;
        // $f = $director->first_name;
        // User::create([
        //     'role_id' => 5,
        //     'fio' => "$f $l",
        //     'email' => $director->login,
        //     'password' => bcrypt('tasar232'),
        //     'mira_id' => null
        // ]);
    }
}
