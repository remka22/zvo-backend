<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public static function create($request)
    {
        $user = $request->user();
        $rec_id = $request->input('rec_id');
        $content = $request->input('text');
        $date = $request->input('date_time');
        $notif = new Notification;
        $notif->user_send_id = $user->id;
        $notif->user_rec_id = $rec_id;
        $notif->content = $content;
        $notif->send_date = $date; //date('Y-m-d H:i:s');
        $notif->is_read = false;
        $notif->save();

        return response([
            'response' => "send notification OK"
        ], 200);
    }

    public static function update($request)
    {
        if ($id = $request->input('notify_id')) {
            $last = $request->input('last_time');
            $notif = Notification::where([['id', $id], ['send_date', '<=', $last]]);
            $notif->is_read = true;
            $notif->save();
        } elseif ($id = $request->input('user_id')) {
            $user = $request->user();
            $notif = Notification::where([['user_rec_id', '=', $user->id], ['user_send_id', '=', $id]])->get();
            foreach ($notif as $n) {
                $n->is_read = true;
                $n->save();
            }
        }
        return response([
            'response' => "notificate read ok"
        ], 200);
    }

    public static function get_last($request)
    {
        $user = $request->user();
        if ($request->get('top')) {
            $notif = Notification::where([['user_rec_id', '=', $user->id], ['is_read', '=', false]])->orderBy('send_date')->get();
        } else {
            $notif = Notification::where([['user_rec_id', '=', $user->id]])->orderBy('send_date')->get();
        }
        $arr_notif = [];
        foreach ($notif as $n) {
            $user_send = User::find($n->user_send_id);
            $arr_notif[] = [
                'id' => $n->id,
                'user_send_id' => $user_send->id,
                'user_send_fio' => $user_send->fio,
                'content' => $n->content,
                'send_date' => $n->send_date,
                'is_read' => $n->is_read
            ];
        }
        return $arr_notif;
    }
    public static function get_users($request)
    {
        $user = $request->user();
        // $users = User::where('id', '<>', $user->id)->whereIn('role_id', [4, 5])->with('new_messages')->get();
        $users = DB::select("select u.id, u.fio, 
                            (select  max(n.send_date) as send_date from notifications n where n.user_send_id = u.id and n.is_read = false),
                            (select  count(n.id) as count from notifications n where n.user_send_id = u.id and n.is_read = false)
                            from users u 
                            where u.role_id in (4,5) and u.id <> " . $user->id . "
                            order by send_date DESC NULLS LAST");
        return $users;
    }

    public static function get_messages($request)
    {
        $user = $request->user();
        $user_rec_id = $request->get('user_rec_id');
        $top = $request->get('top') ?? 10;
        $messages = Notification::where([['user_send_id', '=', $user->id], ['user_rec_id', '=', $user_rec_id]])
                                ->orWhere([['user_send_id', '=', $user_rec_id], ['user_rec_id', '=', $user->id]])
                                ->take($top)
                                ->orderBy('send_date', 'DESC')
                                ->get();
        return $messages;
    }
}
