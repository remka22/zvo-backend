<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // $groups = Group::all();
        // foreach ($groups as $group) {
        //     $cohort = $group->short_name;
        //     for ($i = 0; $i < 20; $i++) {
        //         $campus = DB::connection('pgsql')->select("SELECT max(id), count(id) FROM public.campus");
        //         $count = $campus[0]->count;
        //         $mira_id = $campus[0]->max + 1;
        //         $last_name = $mira_id;
        //         $first_name = "Фио студента";
        //         $login = $mira_id;
        //         $teacher_in_campus = DB::connection('pgsql')->select("INSERT INTO public.campus
        //         (miraid, last_name, first_name, nomz, cohort , subfaculty, faculty, login)
        //         VALUES($mira_id, '$last_name', '$first_name', '$login', '$cohort', null, 'stud', '$login');");
        //     }
        // }

        // $students = DB::connection('pgsql_campus_auth')->select("SELECT * FROM public.campus WHERE faculty = 'stud'");
        // foreach ($students as $stud) {
        //     $user = new User();
        //     $l = $stud->last_name;
        //     $f =$stud->first_name;
        //     $user->role_id = 2;
        //     $user->fio = "$l $f";
        //     $user->email = $stud->login;
        //     $user->moodle_id = null;
        //     $user->mira_id = $stud->miraid;
        //     $user->password = bcrypt('tasar232');
        //     $user->save();
        // }
        // $students = DB::connection('pgsql_campus_auth')->select("SELECT * FROM public.campus WHERE faculty = 'stud'");
        // foreach ($students as $stud) {
        //     $group = Group::where('short_name', $stud->cohort)->get()->first();
        //     $user = User::where('mira_id', $stud->miraid)->get()->first();
        //     $st = new Student();
        //     $st->user_id = $user->id;
        //     $st->group_id = $group->id;
        //     $st->nomz = $stud->nomz;
        //     $st->save();
        // }

        // $students = DB::connection('pgsql_campus_auth')->select("SELECT * FROM public.campus WHERE faculty = 'stud'");
        // foreach ($students as $stud) {
        //     $nomz = $stud->miraid;
        //     DB::connection('pgsql_campus_auth')->select("UPDATE public.campus SET nomz = $nomz WHERE miraid = $nomz");
        // }

        // $students = DB::connection('pgsql_campus_auth')->select("SELECT * FROM public.campus WHERE faculty = 'stud'");
        // foreach ($students as $stud) {
        //     $user = User::where('mira_id', $stud->miraid)->get()->first();
        //     $student = Student::where('user_id', $user->id)->get()->first();
        //     $student->nomz = $stud->nomz;
        //     $student->save();
        // }
    }
}
