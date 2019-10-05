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
