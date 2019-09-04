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
        if (Auth::attempt(['ID' => $phone, 'PW' => $pwd])) {


            return C::RESULT_ARRAY_SUCCESS("성공");
            // Authentication passed...
//            return redirect()->intended('dashboard');
        } else {
            return C::RESULT_ARRAY_ERROR("아이디와 비밀번호가 일치하지 않습니다.");
        }
    }
}
