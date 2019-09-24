<?php


namespace App\Http\Controllers;

use App\Common\C;
use App\Models\Appointment;
use App\Models\Member;
use App\Models\Waiting;
use App\Service\GpsService;
use App\User;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{

    public function CheckInvite()
    {
        $id = request('id',0);

        $waiting = Waiting::where('user_id',$id)->first();

        if(!empty($waiting))
        {
            $appointment = Appointment::find($waiting->appointment_id);
            return C::RESULT_ARRAY_SUCCESS($appointment);

        }else{ //초대가 없는 경우
            return C::RESULT_ARRAY_NG();
        }
    }
    public function getAppointment_detail(){

        $id = request('id',-1);

        $appointment = Appointment::find($id);

        $members = $member = DB::table('Member')->join('users', 'Member.user_id', '=', 'users.id')->where('Member.appointment_id', $appointment->id)->get();

        $appointment->members=$members;

        return C::RESULT_ARRAY_SUCCESS($appointment);
    }

    public function getAppointment(){

        $id = request("id",-1);
        $appointment = Appointment::find($id);

        if($appointment->status == 0) // 약속 대기 중
        {
            return C::RESULT_ARRAY_ERROR($appointment);
        }else if($appointment->status == 1) //약속 실행중
        {
            return C::RESULT_ARRAY_SUCCESS($appointment);
        }else
        {
            return C::RESULT_ARRAY_ERROR($appointment);
        }
    }

    public function acceptInvite(){
        $id = request('id',0);
        $accept = request('accept',0);
        $waiting=Waiting::where('user_id',$id)->first();
        $appointment_id = $waiting->appointment_id;

        if( DB::table('Waiting')->where('user_id',$id)->delete()>0){
            $appointment = Appointment::find($appointment_id);

            if($accept==1) { //수락 했을 때
                $appointment->Members()->create([
                    'user_id' => $id,
                    'Fine_current' => 0,
                    'Fine_final' => 0,
                    'distance' => 0.0,
                    'success' => false,
                ]);
            }
            return C::RESULT_ARRAY_SUCCESS($appointment);
        }
        else{

        } return C::RESULT_ARRAY_NG();
    }


    public function UploadAppointment() //post
    {
        $send_id=request('id', 0);
        $appointment = new Appointment();
        $appointment->address = request('address', '성공회대');
        $appointment->name = request('name','이름 없음');
        $appointment->detail = request('detail', '상세주소');
        $appointment->latitude = request('latitude', '36');
        $appointment->longitude = request('longitude', '126');//
        $appointment->date = date("Y-m-d", strtotime(request('date','')));
        $appointment->date_time = date("H:i", strtotime(request('date_time','')));
        $appointment->radius=100;
        $appointment->Fine_time = request('Fine_time', '300');//3분
        $appointment->Fine_money = request('Fine_money', '500');
        $appointment->Fine_current = 0;
        $appointment->main_user_id = $send_id;
        if ($appointment->save()) {
            //멤버 테이블에 방장을 넣어야 함
            $appointment->Members()->create([
                'user_id'=>request('id',0),
                'Fine_current'=>0,
                'Fine_final'=>0,
                'distance'=>0.0,
                'success'=>false,
            ]);

            $num=(int)request('num',0);
            for($i=0;$i<$num;$i++)
            {
                $receive_id=request('member_id'.$i,0);
                $appointment->waitings()->create([
                    'user_id'=> $receive_id,
                    'date'=> date('Y-m-d H:i'),
                ]);

            }
            return C::RESULT_ARRAY_SUCCESS($appointment);
        } else
        {
            return C::RESULT_ARRAY_ERROR("약속 생성에 실패했습니다.");
        }
    }

    public function test(){
        $appointment = Appointment::get();
        for ($i = 0; $i < sizeof($appointment); $i++) {
            $appointObject = $appointment[$i];    // 약속 객체 한개
            $cur_time = new \DateTime();   // 현재 시간
            $appoint_date = new \DateTime($appointObject->date . ' ' . $appointObject->date_time); // 약속 날짜
            $date_gap = date_diff($cur_time, $appoint_date); // 현재 시간과 약속 시간의 차이
            if ($date_gap->invert == 1) {  // 현재 시간이 약속 시간을 지난 경우 -> 약속 종료 상태로 변경
                if ($appointObject->status == 1) {
                    $member = Member::where('Member.appointment_id', $appointObject->id)->get();
                    //약속 멤버 모두에게 약속 종료 알림 보내기
                    foreach ($member as $object) {
                        $user = User::find($object->user_id);
                        $user->latitude = 0;
                        $user->longitude = 0;
                        if ($object->success == 0) {
                            $object->Fine_final = $object->Fine_current;
                        }
                        $user->save();

                        $object->save();
                    }

//                    NotificationController::SendGameNotification($appointObject->id,$date_gap->h * 60  + $date_gap->i,0,100,$pageTime,array()); //push 알람 보내기
                }
                $appointObject->status = 2; //status가 0이든 1이든 종료상태로
            } else { // 약속 시작 전 또는 진행 중

                // 초 단위 차이
                $time_gap = $date_gap->h * 60 * 60 + $date_gap->i * 60 + $date_gap->s;
                // 설정된 타이머(초)
                $timer = 120 * 60;  //2시간


                // 총 게임 진행 시간(초)
                $totalGameTime = $timer;
                // **약속 실행전**
                if ($appointObject->status == 0) {
                    if ($date_gap->days == 0) { // 현재 날짜와 같음

                        if ($time_gap <= $totalGameTime) {
                            // 시작 상태로 변경하기
                            $appointObject->status = 1;
                            $appointObject->radius = 80000;

                        }
                    }
                } // **약속 실행중**
                else {

                    $appointObject->radius = $appointObject->radius - ((80000 / $timer) * 5);
                    if ($appointObject->radius < 100)
                        $appointObject->radius = 100; //100미터까지

                    $member = Member::join('users', 'Member.user_id', '=', 'users.id')->where('Member.appointment_id', $appointObject->id)->where('Member.success', 0)->get();
                    if($appointObject->Fine_current==0) {

                        $appointObject->Fine_current = $appointObject->Fine_time;


                        foreach ($member as $object) {
                            $distance = GpsService::geoDistance($object->latitude, $object->longitude, $appointObject->latitude, $appointObject->longitude) * 1000;
                            $member_user = Member::where('appointment_id', $appointObject->id)->where('user_id', $object->user_id)->first();
                            if ($appointObject->radius < $distance) { //원 밖에 있다.
//
                                $member_user->update(['Fine_current' => (int)$member_user->Fine_current + $appointObject->Fine_money]);

                            } else if ($distance < 100) //도착
                            {
                                $member_user->update(['success' => 1, 'Fine_current' => 0]);
                            }
                        }
                    }else{
                        $appointObject->Fine_current = $appointObject->Fine_current -5;
                    }
                    NotificationController::SendGameNotification($appointObject->id,$appointObject,$member);
                }

            }
            $appointObject->save(); //반지름 변경
        }
    }
}
