<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;

class TeacherWorkloadController extends Controller
{

    public static function input_teacher_workload(){
        TeacherWorkloadController::teachers_insystem();
        TeacherWorkloadController::input();
    }

    public static function input(){
 
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()
        ->getFont()
        ->setName('Arial')
        ->setSize(14);
        
        
        
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadFilter( new MyReadFilter());
        $spreadsheet = $reader->load('simple.xlsx');
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $data = $sheet->toArray();  
        

        $groups = [];
        foreach($data as $d){
            //if((count(explode(',', $d[2])) > 6) && (count($groups) == 0))
            $groups[] = $d[2];
        }
        //dd($groups);

        foreach($groups as $key => $item){
            if ($arr = explode(',', $item)){
                unset($groups[$key]);
                foreach ($arr as $a){
                    $groups[] = $a;
                }
            }
            
        }
        $groups = array_unique($groups);
        //dd($groups);
        $groups_teachers = [];
        foreach($groups as $item){
           foreach($data as $d){
                if(str_contains($d[2], $item) && !in_array($item, ['', 'Группа'])){
                    $groups_teachers[$item][] = [$d[0], $d[3]];
                }
           } 
        }
        //dd($groups_teachers['НБз-20-1']);
        //dd(explode('-', 'НБз-20-1'));
        foreach($groups_teachers as $gtk => $gtv){
            foreach($gtv as $g){
                //dd($g);
                //dd(date('Y')-2000);
                if (Group::where('short_name', '=', $gtk)->get()->count() == 0){
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

        foreach($groups_teachers as $gtk => $gtv){
            foreach($gtv as $g){
                if ($g[0] != 'Руководство выпускной работой специалиста'){
                    $g_i = explode('-', $gtk);
                    //dd(count($g_i));
                    if (count($g_i) == 3) {
                        $group = Group::where('short_name', '=', $gtk)->get()->first();
                        $teacher = User::where('fio', '=', $g[1])->get()->first();
                        if (Subject::where([['name', '=', $g[0]], ['group_id', '=', $group->id]])->get()->count() == 0){
                            $subject = new Subject;
                            $subject->name = $g[0];
                            $subject->group_id = $group->id;
                            $subject->number_course = date('Y')-2000 - $group->year +1;
                            $subject->save();
                        
                            $subject_teacher = new SubjectTeacher;
                            $subject_teacher->subject_id = $subject->id;
                            $subject_teacher->teacher_id = $teacher->id;
                            $subject_teacher->save();
                        }
                        else{
                            $subject = Subject::where([['name', '=', $g[0]], ['group_id', '=', $group->id]])->get()->first();
                            if (SubjectTeacher::where([['subject_id', '=', $subject->id], ['teacher_id', '=', $teacher->id]])->get()->count() == 0){
                                $subject_teacher = new SubjectTeacher;
                                $subject_teacher->subject_id = $subject->id;
                                $subject_teacher->teacher_id = $teacher->id;
                                $subject_teacher->save();
                            }
                        }
                    }
                    else{
                        $groups = Group::where('short_name', 'like', "%$gtk%")->get();
                        $teacher = User::where('fio', '=', $g[1])->get()->first();
                        foreach ($groups as $gs){
                            if (Subject::where([['name', '=', $g[0]], ['group_id', '=', $gs->id]])->get()->count() == 0){
                                $subject = new Subject;
                                $subject->name = $g[0];
                                $subject->group_id = $gs->id;
                                $subject->number_course = date('Y')-2000 - $gs->year +1;
                                $subject->save();
                            
                                $subject_teacher = new SubjectTeacher;
                                $subject_teacher->subject_id = $subject->id;
                                $subject_teacher->teacher_id = $teacher->id;
                                $subject_teacher->save();
                            }
                            else{
                                $subject = Subject::where([['name', '=', $g[0]], ['group_id', '=', $gs->id]])->get()->first();
                                if (SubjectTeacher::where([['subject_id', '=', $subject->id], ['teacher_id', '=', $teacher->id]])->get()->count() == 0){
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
        
        // $data2 = [];
        // $teacher = '';
        // $group = '';
        // $i = 0;
        // foreach($data as $d){
        //     // if ($teacher != $d[3]){
        //     //     $teacher = $d[3];
        //     //     $data2[] = $teacher;
        //     // }
        //     if($group != $d[2]){
        //         $group = $d[2];
        //         $i++;
        //         $data2[$i][] = $group; 
        //     }
        //     //if(!strpos($d[0], '(установочная)'))
        //     $data2[$i][] = $d[0].' - '.$d[1].' - '.$d[3];
            
        // }

        // dd($data2);
        // $data3 = [];
        // foreach($data2 as $d){
        //     $groups = explode(',', $d[0]);
        //     foreach($groups as $g){
        //         $data3[] = $g;
        //     }
        // }
        // dd($data3);
    }

    public static function teachers_inmoodle(){
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()
        ->getFont()
        ->setName('Arial')
        ->setSize(14);
        
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadFilter( new MyReadFilter());
        $spreadsheet = $reader->load('simple.xlsx');
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $data = $sheet->toArray(); 

        $teachers = [];
        foreach($data as $d){
            if($d[3] != 'Ф.И.О. Преподавателя' && $d[3] != null)
            $teachers[] = $d[3];
        }
        $teachers = array_unique($teachers);
        //dd($teachers);

        foreach ($teachers as $t){
            $fio = explode(" ", $t);
            $first_name = $fio[0];
            $last_name = $fio[1];
            $is_added = DB::connection('pgsql2')->select("select count(id) from public.mdl_user where lastname = '$last_name' and firstname = '$first_name';")[0]->count;
            if ($is_added == 0){
                //dd(0);
                $i_login = DB::connection('pgsql2')->select('select count(id) from public.mdl_user;')[0]->count;
                $i_login++;
                $login = 'p'.$i_login;
                $password = bcrypt('tasar232');
                $email = 'p'.$i_login.'@mail.ru';
                $teacher = DB::connection('pgsql2')->select("INSERT INTO public.mdl_user
                (confirmed, mnethostid, username, password, email, firstname, lastname, city, country, lang)
                VALUES(1, 1, '$login', '$password', '$email', '$first_name', '$last_name', 'Иркутск', 'RU', 'ru');");
                //  dd('ddad');
            }
            //dd(1);
        }
    }
    public static function teachers_insystem(){
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()
        ->getFont()
        ->setName('Arial')
        ->setSize(14);
        
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadFilter( new MyReadFilter());
        $spreadsheet = $reader->load('simple.xlsx');
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $data = $sheet->toArray(); 

        $teachers = [];
        foreach($data as $d){
            if($d[3] != 'Ф.И.О. Преподавателя' && $d[3] != null)
            $teachers[] = $d[3];
        }
        $teachers = array_unique($teachers);
        //dd($teachers);

        foreach ($teachers as $t){
            $is_added = User::where('fio', '=', $t)->get()->count();
            //dd($is_added);
            if ($is_added == 0){
                //dd(0);
                $fio = explode(" ", $t);
                $first_name = $fio[0];
                $last_name = $fio[1];
                $moodle_teacher = DB::connection('pgsql2')->select("select id, email from public.mdl_user where lastname = '$last_name' and firstname = '$first_name';");
                //dd($moodle_teacher);
                if ($moodle_teacher != []){
                    $user = new User;
                    $user->role_id = 3;
                    $user->fio = $t;
                    $user->email = $moodle_teacher[0]->email;
                    $user->moodle_id = $moodle_teacher[0]->id;
                    $user->save();
                }
            }
        }
    }
}

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
    function readCell($columnAddress, $rows, $worksheetName = '') {
        if (($columnAddress == 'A' || $columnAddress=='B' || $columnAddress=='C' || $columnAddress=='D') ) {
            return true;
            }
        return false;
        }
    }

