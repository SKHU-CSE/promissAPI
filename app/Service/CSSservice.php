<?php


namespace App\Service;


class CSSservice
{
    public static function CSSconnect($message)
    {
        return self::GetCSS($message); //현재 mp3데이터 바로 넘기기
    }
    private static function GetCSS($message)
    {
        $client_id = "733fhqom8j";
        $client_secret = "jwxEUZxCO1lKD0c1R1CDcahGlgm4iy7jLZHVRaAP";

        $encText = urlencode($message);
        $postvars = "speaker=mijin&speed=0.3&text=".$encText;
        $url = "https://naveropenapi.apigw.ntruss.com/voice/v1/tts";
        $is_post = true;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true); //바이너리가 있다고 알려
        curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
        $headers = array();
        $headers[] = "X-NCP-APIGW-API-KEY-ID: ".$client_id;
        $headers[] = "X-NCP-APIGW-API-KEY: ".$client_secret;
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "charset: UTF-8";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        return base64_encode($response);
    }
}
