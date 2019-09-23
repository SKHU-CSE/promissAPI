<?php


namespace App\Http\Controllers;

use App\Common\C;
use App\Models\Appointment;
use App\Models\Member;
use App\Models\Waiting;
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

        $members = Member::where('appointment_id',$appointment->id)->get();


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
        $appointment->detail = request('detail', '상세주소');
        $appointment->latitude = request('latitude', '36');
        $appointment->longitude = request('longitude', '126');//
        $appointment->date = date("Y-m-d", strtotime(request('date','')));
        $appointment->date_time = date("H:i", strtotime(request('date_time','')));
        $appointment->radius=100;
        $appointment->Fine_time = request('Fine_time', '300');//3분
        $appointment->Fine_money = request('Fine_money', '500');
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
}
