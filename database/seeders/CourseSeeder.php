<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Moodle_course;
use App\Models\TeacherCourse;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=0; $i < 1801; $i++) { 
            $m_course = new Moodle_course;
            $m_course->link_id = rand(63244, 65324);
            $m_course->name = "Электронны курс$i";
            $m_course->save();
        }

        $teachers = User::where('role_id', 3)->get();
        foreach($teachers as $t){
            for ($i=0; $i < 4; $i++) { 
                $t_course = new TeacherCourse;
                $t_course->user_id = $t->id;
                $t_course->course_id = rand(43, 1843);
                $t_course->save();
            }
        }
    }
}
