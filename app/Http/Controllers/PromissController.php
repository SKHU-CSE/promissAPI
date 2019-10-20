<?php


namespace App\Http\Controllers;


use App\Models\Member;
use App\Service\ChatBotService;
use App\Service\CSSservice;
use App\Service\GpsService;
use App\Common\C;

use App\Models\Appointment;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppointmentController;

class PromissController
{
    public function PromissChat()
    {
        $message =request("message","");
        $id=request('id',0);
        $getSendMessage=ChatBotService::ChatbotMessage($id,$message);
        $appointment=Member::join('appointment','Member.appointment_id','=','appointment.id')->where('Member.user_id',$id)->where('appointment.status','!=',2)->first();
        switch ($getSendMessage)
        {
            case '인사':
                $getSendMessage= '약속 지킴이 프로미스입니다.';
                break;
            case '약속 장소 문의':
                if(empty($appointment))
                    $getSendMessage= '현재 약속을 하고 계시지 않습니다. 약속을 만들어주세요.';
                else {
                    $address = $appointment->address;

                    $getSendMessage = $appointment->name . '의 약속 장소는 ' .$address.'입니다.';
                }
                break;
            case '약속시간 문의':
                if(empty($appointment))
                    $getSendMessage= '현재 약속을 하고 계시지 않습니다. 약속을 만들어주세요.';
                else
                    $getSendMessage= $appointment->name.'의 약속 시간은 '.$appointment->date_time.'입니다.';
                break;
            case '남은 약속 시간':
                if(empty($appointment))
                    $getSendMessage= '현재 약속을 하고 계시지 않습니다. 약속을 만들어주세요.';
                else{
                    $difference=date_diff(new \DateTime(),new \DateTime($appointment->date.' '.$appointment->date_time));
                    $getSendMessage='현재 고객님의 남은 시간은 ';
                    $hour=$difference->h;
                    $min=$difference->i;
                    if($hour!=0)
                        $getSendMessage =$getSendMessage.$hour.' 시간';
                    $getSendMessage= $getSendMessage.$min.' 분 남았습니다.';
                }
                break;
            case '친구 위치':
                //약속장소에 가장 가까운 약속친구는 현재 어디에 위치하고 있으며, 약속장소까지 몇남았습니다.
                //멀리있는
                if($appointment->status == 0)
                    $getSendMessage= '현재 약속을 실행중이지 않습니다. 기다려주세요';
                else {
                    $user=null;

                    $address =$appointment->name;
                    $minDistance=1000000000000;
                    $member = Member::join('users', 'Member.user_id', '=', 'users.id')->where('Member.appointment_id', $appointment->id)->where('Member.success',0)->get();
                    foreach ($member as $object) { //약속 멤버 모두에게 gps 알림 보내기
                        $distance = (int)(GpsService::geoDistance($object->latitude, $object->longitude, $appointment->latitude, $appointment->longitude) * 1000);
                        if ($minDistance > $distance) {
                            $minDistance=$distance;
                            $user=$object;
                        }
                    }
                    $getSendMessage='약속 장소에 가장 가까운'.$user->name.'이며, 약속 장소까지'.$minDistance.'미터 남은 상태입니다.';
                }
                break;
            case '내 위치':
                if(empty($appointment))
                    $getSendMessage= '현재 약속을 하고 계시지 않습니다. 약속을 만들어주세요.';
                else {
                //    $address = ReverseGeoCoadingService::Geoconnect($appointment->longitude . ',' . $appointment->latitude);
                    $address = "테스트";
                    $getSendMessage = '현재 고객님의 위치는 ' . $address . '입니다.';
                }
                break;
            case '거리 문의':
                if($appointment->status ==0)
                    $getSendMessage= '현재 약속을 실행중이지 않습니다. 기다려주세요';
                else {
                    $user = User::find($id);
                    $distance = (int)(GpsService::geoDistance($user->latitude, $user->longitude, $appointment->latitude, $appointment->longitude) * 1000);
                    //약속이름의 약속 장소까지 몇 남았으며 약속 시간까지 몇남앗습니다.
                    $getSendMessage=$appointment->name.'의 약속장소까지 '.$distance.'미터 남았습니다.';
                }
                break;
        }
        $mp3 =CSSService::CSSconnect($getSendMessage);
        $Promiss=array("message"=>$getSendMessage,"voice"=>$mp3);
        if($getSendMessage=='질문을 잘 이해하지 못했어요')
            return C::RESULT_ARRAY_ERROR_2("faild",$Promiss);
        return C::RESULT_ARRAY_SUCCESS($Promiss);
    }

}
