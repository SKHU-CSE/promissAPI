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
        if (User::where('ID',$phone)->where('PW',$pwd)->exists()){
            return C::RESULT_ARRAY_SUCCESS("성공.");
        } else {
            return C::RESULT_ARRAY_ERROR("아이디와 비밀번호가 일치하지 않습니다.");
        }
    }
}
