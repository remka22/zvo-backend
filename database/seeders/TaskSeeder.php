<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MoodleCourse;
use App\Models\MoodleTask;
use App\Models\Teacher_course;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $m_course = MoodleCourse::all();
        // foreach($m_course as $mc){
        //     for ($i=0; $i < rand(3,7); $i++) { 
        //         $m_course = new MoodleTask;
        //         $m_course->link_id = rand(63244, 85324);
        //         $m_course->name = "Задание $i";
        //         $m_course->type = "assign";
        //         $m_course->course_id = $mc->id;
        //         $m_course->save();
        //     }
        // }
    }
}
