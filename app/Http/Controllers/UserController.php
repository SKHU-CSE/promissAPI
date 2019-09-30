<?php


namespace App\Http\Controllers;

use App\User;
use Auth;
use App\Common\C;
use App\Models\Appointment;
use App\Models\Member;

class UserController extends Controller
{
    public function userLogin()
    {
        $phone=request('id','');
        $pwd=request('pw','');
        if (User::where('user_name',$phone)->where('user_pw',$pwd)->exists()){
            $user = User::where('user_name',$phone)->where('user_pw',$pwd)->first();
            if(Member::join('appointment','Member.appointment_id','=','appointment.id')->where('appointment.status','!=',2)->exists())
            {

                $member = Member::join('appointment','Member.appointment_id','=','appointment.id')->where('appointment.status','!=',2)->first();
                $user->appointment_id = $member->appointment_id;
            }else
            {
                $user->appointment_id = -1;
            }
            return C::RESULT_ARRAY_SUCCESS($user);
        } else {
            return C::RESULT_ARRAY_ERROR("아이디와 비밀번호가 일치하지 않습니다.");
        }
    }



    public function userDelete(){

        $id = request('index',0);
        $user=User::where('id',$id)->first();

        if($user==null) return C::RESULT_ARRAY_NG();

        if($user->delete())return C::RESULT_ARRAY_OK();
        else return C::RESULT_ARRAY_NG();
    }

    public function userChangePassword(){
        $id= request('index','');
        $newPassword=request('password','');

        $user=User::where('id',$id)->first();

        if($user==null) return C::RESULT_ARRAY_ERROR("사용자가 존재하지 않습니다");

        $user->user_pw=$newPassword;
        if($user->save())
            return C::RESULT_ARRAY_SUCCESS($user);
        else
            return C::RESULT_ARRAY_ERROR("비밀번호 변경에 실패하였습니다.");

    }


    public function SearchUser(){
        $id = request('ID','');
        $user=User::where('user_name','regexp',$id.'[a-zA-Z]*')->get();
        return C::RESULT_ARRAY_SUCCESS($user);

    }

    public function UploadGPS(){
        $id=request('id',0);

        $user = User::find($id);
        if(empty($user))
            return C::RESULT_ARRAY_ERROR("현재 회원님의 회원 정보가 존재하지 않습니다.");
        $user->longitude=request('longitude',0.0);
        $user->latitude=request('latitude',0.0);
        if($user->save())
            return C::RESULT_ARRAY_OK();
        else
            return C::RESULT_ARRAY_ERROR("현재 DB서버가 문제가 있어 관리자에게 문의를 주시길 바랍니다.");
    }

    public function userRegister(){

        $id = request('id','');
        if(User::where('user_name',$id)->exists())
        {
            return C::RESULT_ARRAY_ERROR("ID중복");
        }else{
            $pwd = request('pw','');
            $user= new User();
            $user->user_name=$id;
            $user->user_pw=$pwd;
            $user->latitude=0;
            $user->longitude=0;
            $user->last_date=date("Y-m-d");
            if($user->save()){
                return C::RESULT_ARRAY_SUCCESS($user);
            }else{
                return C::RESULT_ARRAY_ERROR("현재 서버 점검 중으로 회원가입 기능을 이용하실 수 없습니다.");
            }
        }

    }
}
