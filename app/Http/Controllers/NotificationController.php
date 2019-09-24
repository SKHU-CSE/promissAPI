<?php


namespace App\Http\Controllers;


class NotificationController extends Controller
{


    public static function SendGameNotification($id,$data)
    {
        //알림 보내기

        $options = array(
            'cluster' => 'ap3',
            'useTLS' => true
        );
        $pusher = new Pusher\Pusher(
            'cb4bcb99bfc3727bdfb0',
            '6cb5dd11536445d1355c',
            '867663',
            $options
        );
//
//        $data['time']=$time;
//        $data['appoint_radius']=$appoint_radius;
//        $data['totalTime']=$total;
        $pusher->trigger('ProMiss', 'event_game'.$id, $data);
    }
}
