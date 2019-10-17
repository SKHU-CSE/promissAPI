<?php
namespace App\Common;
class C
{
    //lm	j20j3<
    const RESULT_OK = "OK";
    const RESULT_NG = "NG";
    const RESULT_YES= "YES";
    const RESULT_NO = "NO";
    public static function RESULT_ARRAY_OK() {
        return array(
            "result"=>self::RESULT_OK
        );
    }
    public static function RESULT_ARRAY_NG() {
        return array(
            "result"=>self::RESULT_NG
        );
    }
    const RESULT_ERROR = 1000;
    const RESULT_SUCCESS = 2000;
    public static function RESULT_ARRAY_SUCCESS( $arrData = [] ) {
        return array(
            "result" => self::RESULT_SUCCESS,
            "data" => $arrData
        );
    }
    public static function RESULT_ARRAY_ERROR($message=null) {
        return array(
            "result" => self::RESULT_ERROR,
            'message' => $message,
            "data" => null
        );
    }

    public static function RESULT_ARRAY_ERROR_2($message=null,$data=[]){
        return array(
            "result" => self::RESULT_ERROR,
            'message' => $message,
            "data" => $data

        );
    }
}
