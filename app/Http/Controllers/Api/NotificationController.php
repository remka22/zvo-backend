<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public static function create($request){
        $id_user = $request->input('id');
        $notificats = $request->input('notificate');
        foreach ($notificats as $n){
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

    public static function get(){
        
    }
}
