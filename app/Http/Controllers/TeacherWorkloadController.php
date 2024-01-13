<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Models\Moodle\MdlUser;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class TeacherWorkloadController extends Controller
{

    public static function test_input()
    {
        $path = "../storage/app/workload/workload.xlsx";

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setSize(14);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadFilter(new MyReadFilter());
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $data = $sheet->toArray();

        dd($data);
    }

    public static function input_teacher_workload($request)
    {
        $file = $request->file('file');
        $path = Storage::putFileAs('workload', $file, 'workload.xlsx');
        $path = "../storage/app/" . $path;

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setSize(14);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadFilter(new MyReadFilter());
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $data = $sheet->toArray();

        // TeacherWorkloadController::teachers_inmoodle($data);
        // TeacherWorkloadController::teachers_incampus($data);
        // TeacherWorkloadController::teachers_insystem($data);
        TeacherWorkloadController::input($data);

        response('Нагрузка загружена');
    }

    public static function input($data)
    {

        $groups = [];
        foreach ($data as $d) {
            //if((count(explode(',', $d[2])) > 6) && (count($groups) == 0))
            $groups[] = $d[2];
        }
        //dd($groups);

        foreach ($groups as $key => $item) {
            if ($arr = explode(',', $item)) {
                unset($groups[$key]);
                foreach ($arr as $a) {
                    $groups[] = $a;
                }
            }
        }
        $groups = array_unique($groups);
        //dd($groups);
        $groups_teachers = [];
        foreach ($groups as $item) {
            foreach ($data as $d) {
                if (str_contains($d[2], $item) && !in_array($item, ['', 'Группа'])) {
                    $groups_teachers[$item][] = [$d[0], $d[3]];
                }
            }
        }
        //dd($groups_teachers['НБз-20-1']);
        //dd(explode('-', 'НБз-20-1'));
        foreach ($groups_teachers as $gtk => $gtv) {
            foreach ($gtv as $g) {
                //dd($g);
                //dd(date('Y')-2000);
                if (Group::where('short_name', '=', $gtk)->get()->count() == 0) {
                    $g_i = explode('-', $gtk);
                    //dd(count($g_i));
                    if (count($g_i) == 3) {
                        $group = new Group;
                        $group->short_name = $gtk;
                        $group->year = intval($g_i[1]);
                        $group->number = intval($g_i[2]);
                        $group->save();
                    }
                }
            }
        }
        //dd(Group::where('short_name', 'like', "%РДбз-22%")->get());

        foreach ($groups_teachers as $gtk => $gtv) {
            foreach ($gtv as $g) {
                if ($g[0] != 'Руководство выпускной работой специалиста') {
                    $g_i = explode('-', $gtk);
                    //dd(count($g_i));
                    if (count($g_i) == 3) {
                        $group = Group::where('short_name', '=', $gtk)->get()->first();
                        $teacher = User::where('fio', '=', $g[1])->get()->first();
                        if (Subject::where([['name', '=', $g[0]], ['group_id', '=', $group->id]])->get()->count() == 0) {
                            $subject = new Subject;
                            $subject->name = $g[0];
                            $subject->group_id = $group->id;
                            $subject->number_course = date('Y') - 2000 - $group->year + 1;
                            $subject->save();
                            if ($teacher != null) {
                                $subject_teacher = new SubjectTeacher;
                                $subject_teacher->subject_id = $subject->id;
                                $subject_teacher->teacher_id = $teacher->id;
                                $subject_teacher->save();
                            }
                        } else if ($teacher != null) {
                            $subject = Subject::where([['name', '=', $g[0]], ['group_id', '=', $group->id]])->get()->first();
                            if (SubjectTeacher::where([['subject_id', '=', $subject->id], ['teacher_id', '=', $teacher->id]])->get()->count() == 0) {
                                $subject_teacher = new SubjectTeacher;
                                $subject_teacher->subject_id = $subject->id;
                                $subject_teacher->teacher_id = $teacher->id;
                                $subject_teacher->save();
                            }
                        }
                    } else {
                        $groups = Group::where('short_name', 'like', "%$gtk%")->get();
                        $teacher = User::where('fio', '=', $g[1])->get()->first();
                        foreach ($groups as $gs) {
                            if (Subject::where([['name', '=', $g[0]], ['group_id', '=', $gs->id]])->get()->count() == 0) {
                                $subject = new Subject;
                                $subject->name = $g[0];
                                $subject->group_id = $gs->id;
                                $subject->number_course = date('Y') - 2000 - $gs->year + 1;
                                $subject->save();
                                if ($teacher != null) {
                                    $subject_teacher = new SubjectTeacher;
                                    $subject_teacher->subject_id = $subject->id;
                                    $subject_teacher->teacher_id = $teacher->id;
                                    $subject_teacher->save();
                                }
                            } else if ($teacher != null) {
                                $subject = Subject::where([['name', '=', $g[0]], ['group_id', '=', $gs->id]])->get()->first();
                                if (SubjectTeacher::where([['subject_id', '=', $subject->id], ['teacher_id', '=', $teacher->id]])->get()->count() == 0) {
                                    $subject_teacher = new SubjectTeacher;
                                    $subject_teacher->subject_id = $subject->id;
                                    $subject_teacher->teacher_id = $teacher->id;
                                    $subject_teacher->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public static function teachers_inmoodle($data)
    {


        $teachers = [];
        foreach ($data as $d) {
            if ($d[3] != 'Ф.И.О. Преподавателя' && $d[3] != null)
                $teachers[] = [$d[3], $d[13]];
        }
        $teachers = array_unique($teachers, SORT_REGULAR);
        //dd($teachers);

        foreach ($teachers as $t) {
            $fio = explode(" ", $t[0]);
            $first_name = $fio[0];
            $last_name = $fio[1];
            $faculty = $t[1];
            $cohort = "Преподаватель";
            $moodle_teacher = DB::connection('pgsql_moodle')->select("select id, email from public.mdl_user where lastname = '$last_name' and firstname = '$first_name';");
            if ($moodle_teacher == []) {
                //dd(0);
                $i_login = DB::connection('pgsql_moodle')->select('select count(id) from public.mdl_user;')[0]->count;
                $i_login++;
                $login = 'p' . $i_login;
                $password = bcrypt('tasar232');
                $email = 'p' . $i_login . '@mail.ru';
                $teacher = DB::connection('pgsql_moodle')->select("INSERT INTO public.mdl_user
                (confirmed, mnethostid, username, password, email, firstname, lastname, city, country, lang)
                VALUES(1, 1, '$login', '$password', '$email', '$first_name', '$last_name', 'Иркутск', 'RU', 'ru');");

                $moodle_teacher = DB::connection('pgsql_moodle')->select("select id, email from public.mdl_user where lastname = '$last_name' and firstname = '$first_name';");
                $mira_id = $moodle_teacher[0]->id + 1122345;


                $teacher_in_campus = DB::connection('pgsql_campus_auth')->select("INSERT INTO public.campus
                (miraid, last_name, first_name, nomz, cohort , subfaculty, faculty, login)
                VALUES($mira_id, '$last_name', '$first_name', null, '$cohort', null, '$faculty', '$login');");
                //  dd('ddad');
            } else {
                $count_teacher_campus_auth = DB::connection('pgsql_campus_auth')->select("SELECT miraid FROM public.campus WHERE last_name = '$last_name' and first_name = '$first_name' and faculty = '$faculty'");
                if ($count_teacher_campus_auth == []) {
                    $mira_id = $moodle_teacher[0]->id + 1122345;
                    $login = $moodle_teacher[0]->email;
                    $teacher_in_campus = DB::connection('pgsql_campus_auth')->select("INSERT INTO public.campus
                (miraid, last_name, first_name, nomz, cohort , subfaculty, faculty, login)
                VALUES($mira_id, '$last_name', '$first_name', null, '$cohort', null, '$faculty', '$login');");
                }
            }
            //dd(1);
        }
    }

    public static function teachers_incampus($data)
    {

        $teachers = [];
        foreach ($data as $d) {
            if ($d[3] != 'Ф.И.О. Преподавателя' && $d[3] != null)
                $teachers[] = [$d[3], $d[13]];
        }
        $teachers = array_unique($teachers, SORT_REGULAR);
        //dd($teachers);

        foreach ($teachers as $t) {
            $fio = explode(" ", $t[0]);
            $first_name = $fio[0];
            $last_name = $fio[1];
            $subaculty = $t[1];
            $cohort = "Преподаватель";
            $teacher_campus = Campus::where([['first_name', '=', $first_name], ['last_name', '=', $last_name]])->get();
            if (count($teacher_campus) > 1) {
                //check the subfuculty
            } else if (count($teacher_campus) == 0) {
                $moodle_teacher = MdlUser::where([['lastname', '=', $last_name], ['firstname', '=', $first_name]])->get();
                if (count($moodle_teacher) > 1) {
                    //check the subfuculty in a moodle
                } else if (count($moodle_teacher) == 1) {
                    $m_teacher = $moodle_teacher->first();
                    $campus = new Campus();
                    $campus->miraid = null;
                    $campus->last_name = $m_teacher->lastname;
                    $campus->first_name = $m_teacher->firstname;
                    $campus->nomz = $m_teacher->username;
                    $campus->cohort  = 'Преподаватель';
                    $campus->subfaculty = $subaculty;
                    $campus->faculty = null;
                    $campus->login = $m_teacher->username;
                    $campus->save();
                } else {
                    //dont undestend what we will do
                }
            }
            //dd(1);
        }
    }

    public static function teachers_insystem($data)
    {
        $teachers = [];
        foreach ($data as $d) {
            if ($d[3] != 'Ф.И.О. Преподавателя' && $d[3] != null)
                $teachers[] = [$d[3], $d[13]];
        }
        $teachers = array_unique($teachers, SORT_REGULAR);
        //dd($teachers);

        foreach ($teachers as $t) {
            $fio = explode(" ", $t[0]);
            $first_name = $fio[0];
            $last_name = $fio[1];
            $subfaculty = $t[1];

            $user = User::where('fio', '=', $t[0])->get();
            if ($user->count() == 0) {
                $teacher_campus = Campus::where([['first_name', '=', $first_name], ['last_name', '=', $last_name]])->get();
                $user = new User;
                $save = true;
                $user->role_id = 3;
                if ($teacher_campus->count() == 0) {
                    $moodle_teacher = MdlUser::where(['firstname', '=', $first_name], ['lastname', '=', $last_name])->get();
                    //dd($moodle_teacher);
                    if ($moodle_teacher->count() == 1) {
                        $moodle_teacher = $moodle_teacher->first();
                        $user->fio = $t[0];
                        $user->email = $moodle_teacher->username;
                        $user->moodle_id = $moodle_teacher->id;
                        $user->password = $moodle_teacher->password; //needs a generation
                    } else {
                        $user->fio = $t[0] + 'not_found in_moodle_and_in_campus';
                        $user->email = 'NotFound inMoodle';
                        $user->moodle_id = null;
                        $user->password = null;
                    }
                    $user->mira_id = null;
                } else if ($teacher_campus->count() == 1) {
                    $teacher_campus = $teacher_campus->first();
                    // $moodle_teacher = DB::connection('pgsql_moodle')->select("select id, email, password from public.mdl_user where lastname = '$last_name' and firstname = '$first_name';");
                    $moodle_teacher = MdlUser::where('username', $teacher_campus->login)->get()->first();
                    //dd($moodle_teacher);
                    if ($moodle_teacher != null) {
                        $user->fio = $t[0];
                        $user->moodle_id = $moodle_teacher->id;
                        $user->password = $moodle_teacher->password; //needs a generation
                    } else {
                        $user->fio = $t[0] + 'NotFound inMoodle';
                        $user->moodle_id = null;
                        $user->password = null; //needs a generation
                    }
                    $user->email = $teacher_campus->login;
                    $user->mira_id = $teacher_campus->miraid;
                } else {
                    $teacher_campus = Campus::where([['first_name', '=', $first_name], ['last_name', '=', $last_name], ['subfaculty', '=', $subfaculty]])->get();
                    if ($teacher_campus->count() == 0) {
                        //go to moodle and check subfaculty
                    } else if ($teacher_campus->count() == 1) {
                        $teacher_campus = $teacher_campus->first();
                        // $moodle_teacher = DB::connection('pgsql_moodle')->select("select id, email, password from public.mdl_user where lastname = '$last_name' and firstname = '$first_name';");
                        $moodle_teacher = MdlUser::where('username', $teacher_campus->login)->get()->first();
                        //dd($moodle_teacher);
                        if ($moodle_teacher != null) {
                            $user->fio = $t[0];
                            $user->moodle_id = $moodle_teacher->id;
                            $user->password = $moodle_teacher->password; //needs a generation
                        } else {
                            $user->fio = $t[0] + 'NotFound inMoodle';
                            $user->moodle_id = null;
                            $user->password = null; //needs a generation
                        }
                        $user->email = $teacher_campus->login;
                        $user->mira_id = $teacher_campus->miraid;
                    } else {
                        //no have idea
                    }
                }

                if ($save) {
                    $user->save();
                }
            }
        }
    }
}

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    function readCell($columnAddress, $rows, $worksheetName = '')
    {
        if (($columnAddress == 'A' || $columnAddress == 'B' || $columnAddress == 'C' || $columnAddress == 'D' || $columnAddress == 'N')) {
            return true;
        }
        return false;
    }
}
