<?php

namespace App\Http\Controllers\Api;

use App\Models\Moodle\MdlQuizGrades;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Moodle\MdlAssignGrades;
use App\Models\Moodle\MdlCourseModules;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;
use App\Models\TeacherCourse;
use App\Models\MoodleCourse;
use App\Models\NeedsTask;
use App\Models\MoodleTask;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public static function get_subjects($request)
    {
        $user = $request->user();
        // if ($user->role_id != 2) {
        //     return response(
        //         ['massage' => 'ограничены права доступа'],
        //         500
        //     );
        // }
        // $student = Student::where('user_id', $user->id)->get()->first();
        $group = Group::where('id', $user->group_id)->get()->first();
        if ($group->metodist_id) {
            $m = User::find($group->metodist_id);
            $metodist_id = $m->id;
            $metodist_fio = $m->fio;
            $metodist_email = $m->email;
        }
        $number_course = date("Y") - $group->year;
        if (date("m") > 9) {
            $number_course++;
        }
        $number_course -= 2000;
        $arr_group = [
            'name' => $group->id,
            'metodist_id' => $metodist_id ?? null,
            'metodist_fio' => $metodist_fio ?? null,
            'metodist_email' => $metodist_email ?? null,
            'year' => $group->year,
            'course' => $number_course,
            'number' => $group->number
        ];
        $subjects = Subject::where([['group_id', '=', $group->id], ['number_course', '=', $number_course]])->orderBy('subject_teacher_id')->get();

        $data = [
            'group' => $arr_group,
            'subjects' => $subjects
        ];
        // json_encode($data, JSON_UNESCAPED_UNICODE)
        //dd($data);
        return (json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    public static function get_subject_data($request)
    {
        $user = $request->user();

        $sub = Subject::where([['group_id', '=', $user->group_id], ['id', '=', $request->get('subject_id')]])->first();



        $arr_sabject = [];

        if ($sub->subject_teacher_id != null) {
            $t_subject = SubjectTeacher::find($sub->subject_teacher_id);
            $teacher = User::find($t_subject->teacher_id);
            if ($t_course = TeacherCourse::find($t_subject->teacher_course_id)) {
                $course = MoodleCourse::find($t_course->course_id);
                $need_task = NeedsTask::where('subject_id', $t_subject->id)->get();
                if ($need_task) {
                    foreach ($need_task as $t) {
                        $arr_id_tasks[] = $t->task->link_id;
                    }
                    $grads = DB::connection('pgsql_moodle')->select("select mcm.id, mgi.itemmodule as type , mgg.finalgrade as grade, mgg.rawgrademax as maxgrade, mgg.usermodified as userid, 
                                                                             mcm.id as link_id, mgi.itemname as name
                                                                         from mdl_course_modules as mcm
                                                                         inner join mdl_grade_items as mgi on mgi.iteminstance = mcm.instance
                                                                         inner join mdl_grade_grades as mgg on mgg.itemid = mgi.id
                                                                         where mcm.id in (" . implode(",", $arr_id_tasks) . ") and mgg.userid = " . $user->moodle_id);
                }
            }
            $arr_sabject = [
                'id' => $sub->id,
                'name' => $sub->name,
                'comment' => $t_subject->comment,
                'number_course' => $sub->number_course,
                'name_course' => $course->name ?? 'Не указан',
                'id_link' => $course->link_id ?? '0',
                'need_task' => $need_task ?? [],
                'grads' => $grads ?? [],
                'teacher_fio' => $teacher->fio,
            ];
        }
        $data = [
            'subject' => $arr_sabject
        ];
        // json_encode($data, JSON_UNESCAPED_UNICODE)
        //dd($data);
        return $data; //(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
