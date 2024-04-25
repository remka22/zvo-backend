<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
    public static function create($request)
    {
        $id_user = $request->input('id');
        $notificats = $request->input('notificate');
        foreach ($notificats as $n) {
            $notif = new Notification;
            $notif->user_send_id = $id_user;
            $notif->user_rec_id = $n['id_user_rec'];
            $notif->content = $n['content'];
            $notif->send_date = date($n['send_date']);
            $notif->is_read = false;
            $notif->save();
        }
        return response([
            'response' => "send notification OK"
        ], 200);
    }

    public static function update($request)
    {
        $id = $request->input('notify_id');
        $notif = Notification::find($id);
        $notif->is_read = true;
        $notif->save();
        return response([
            'response' => "notificate read ok"
        ], 200);
    }

    public static function get($request)
    {
        $user = $request->user();
        if ($request->get('top')){
            $notif = Notification::where([['user_rec_id', '=', $user->id], ['is_read', '=', false]])->orderBy('send_date')->get();
        }else{
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
}
