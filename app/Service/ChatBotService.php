<?php


namespace App\Service;


class ChatBotService
{
    public static function ChatbotMessage($id,$message)
    {
        $data_array = json_decode(self::GetMessageformChatbot($id,$message), true);
        return $data_array["content"][0]["data"]["details"];
//           return $data_array;
    }
    public static function GetMessageformTestChatbot($id,$message) //테스트용
    {
        $sendData=array(array("type"=>"text","data"=>array("description"=>$message)));
        //헤더가 틀리다고 나옴  hash_hmac 틀리다고 나옴
        $postvars =json_encode(array("userId"=>$id,"timestamp"=>"12345678","user_ip"=>'8.8.8.8',
            "version"=> "v2","bubbles"=>$sendData,"event"=>"send"));
        $url = "https://j106gflv8r.apigw.ntruss.com/promiss/beta/";
        $is_post = true;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
        $headers = array();
        $headers[] = "Content-Type:application/json";
        $sig = hash_hmac('sha256',$postvars, 'SFpBd3NFcUxvaHBSTW5TbG5QeFNQUXFCZFhQRURPek8=');
        $headers[] = "X-NCP-CHATBOT_SIGNATURE:".base64_encode($sig);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        return $response;
    }

    private static function GetMessageformChatbot($id,$message)
    {
        $sendData=array(array("type"=>"text","data"=>array("details"=>$message)));
        $postvars =json_encode(array("userId"=>$id,"timestamp"=>"12345678",
            "version"=> "v1","content"=>$sendData,"event"=>"send"));
        $url = "https://j106gflv8r.apigw.ntruss.com/promiss/beta/";
        $is_post = true;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
        $headers = array();
        $headers[] = "Content-Type:application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        return $response;
    }
}
