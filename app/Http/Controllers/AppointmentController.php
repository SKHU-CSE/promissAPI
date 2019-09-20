<?php


namespace App\Http\Controllers;

use App\Common\C;
use App\Models\Appointment;

class AppointmentController extends Controller
{

    public function UploadAppointment() //post
    {
        $send_id=request('id', 0);
        $appointment = new Appointment();
        $appointment->address = request('address', '성공회대');
        $appointment->detail = request('detail', '상세주소');
        $appointment->latitude = request('latitude', '36');
        $appointment->longitude = request('longitude', '126');//
        $appointment->date = date("Y.m.d", strtotime(request('date','')));
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
                'Fine final'=>0,
                'distance'=>0.0,
                'success'=>false,
            ]);

            $num=(int)request('num',0);
            for($i=0;$i<$num;$i++)
            {
                $receive_id=request('member_id'.$i,0);
                $appointment->wattings()->create([
                    'user_id'=> $receive_id,
                    'date'=> date('Y-m-d H:i'),
                ]);

            }
            return C::RESULT_ARRAY_SUCCESS("약속 생성에 성공하였습니다.");
        } else
        {
            return C::RESULT_ARRAY_ERROR("약속 생성에 실패했습니다.");
        }
    }
}
