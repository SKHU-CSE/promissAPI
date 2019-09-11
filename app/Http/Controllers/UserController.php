<?php


namespace App\Http\Controllers;

use App\User;
use Auth;
use App\Common\C;

class UserController extends Controller
{
    public function userLogin()
    {
        $phone=request('id','');
        $pwd=request('pw','');
        if (User::where('user_id',$phone)->where('user_pw',$pwd)->exists()){
            $user = User::where('user_id',$phone)->where('user_pw',$pwd)->first();
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

    public function userRegister(){

        $id = request('id','');
        if(User::where('ID',$id)->exists())
        {
            return C::RESULT_ARRAY_ERROR("ID중복");
        }else{
            $pwd = request('pw','');
            $user= new User();
            $user->user_id=$id;
            $user->user_pw=$pwd;
            $user->last_date=date("Y-m-d");
            if($user->save()){
                return C::RESULT_ARRAY_OK();
            }else{
                return C::RESULT_ARRAY_ERROR("현재 서버 점검 중으로 회원가입 기능을 이용하실 수 없습니다.");
            }
        }

    }
}
