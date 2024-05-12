<?php

namespace Database\Seeders;

use App\Models\Moodle\MdlAssign;
use App\Models\Moodle\MdlCourse;
use App\Models\Moodle\MdlCourseModules;
use App\Models\Moodle\MdlEnrol;
use App\Models\Moodle\MdlQuiz;
use App\Models\Moodle\MdlUser;
use App\Models\Moodle\MdlUserEnrolments;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MoodleCourse;
use App\Models\TeacherCourse;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $users = MdlUser::where('city', 'Иркутск')->get();
        // $id_last_course = MdlCourse::all()->first()->id;
        // // dd($id_last_course);
        // foreach ($users as $u){
        //     // $u = User::find(6);
        //     for ($i=0; $i < 5; $i++) { 
        //         $course = new MdlCourse();
        //         $ncur = $id_last_course+1;
        //         $course->category = 1;
        //         $course->summaryformat = 1;
        //         $course->fullname = "Электронны курс $ncur";
        //         $course->shortname = "эл кур $ncur";
        //         $course->summary = 'тест';
        //         $course->enablecompletion = 1;
        //         $course->startdate = 1704988800;
        //         $course->enddate = 1736524800;
        //         $course->cacherev = 1704969712;
        //         $course->sortorder = 10001;
        //         $course->save();
                
        //         $enrol = new MdlEnrol();
        //         $enrol->courseid = $course->id;
        //         $enrol->name = "тест";
        //         $enrol->enrol = "manual";
        //         $enrol->roleid = 5;
        //         $enrol-> expirythreshold = 86400;
        //         $enrol->save();

        //         $user_enrol = new MdlUserEnrolments();
        //         $user_enrol->enrolid = $enrol->id;
        //         $user_enrol->userid = $u->id;
        //         $user_enrol->modifierid = 2;
        //         $user_enrol->timestart = 1704964899;
        //         $user_enrol->save();

        //         for ($j=0; $j < 3; $j++) { 
        //             $ncur = "$j $course->id";

        //             $assign = new MdlAssign();
        //             $assign->course = $course->id;
        //             $assign->name = "Задание $ncur";
        //             $assign->intro = 'тест';
        //             $assign->save();

        //             $course_module = new MdlCourseModules();
        //             $course_module->course = $course->id;
        //             $course_module->module = 1;
        //             $course_module->instance = $assign->id;
        //             $course_module->save();

        //             $quiz = new MdlQuiz();
        //             $quiz->course = $course->id;
        //             $quiz->name = "Тест $ncur";
        //             $quiz->intro = 'тест';
        //             $quiz->save();

        //             $course_module = new MdlCourseModules();
        //             $course_module->course = $course->id;
        //             $course_module->module = 17;
        //             $course_module->instance = $quiz->id;
        //             $course_module->save();
        //         }
        //         $id_last_course = $course->id;
        //     }
        // }

        // for ($i=0; $i < 1801; $i++) { 
        //     $m_course = new MoodleCourse;
        //     $m_course->link_id = rand(63244, 65324);
        //     $m_course->name = "Электронны курс$i";
        //     $m_course->save();
        // }

        // $teachers = User::where('role_id', 3)->get();
        // foreach($teachers as $t){
        //     for ($i=0; $i < 4; $i++) { 
        //         $t_course = new TeacherCourse;
        //         $t_course->user_id = $t->id;
        //         $t_course->course_id = rand(43, 1843);
        //         $t_course->save();
        //     }
        // }
    }
}
