<?php
namespace App\Helpers;

class Helper
{
    public static function getJsonFromUrlDecode(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $arquivo_json = curl_exec ($ch);
        $error = curl_error($ch);  //if you need
        curl_close ($ch);

        if($arquivo_json){
            return json_decode($arquivo_json, true);
        }else{
            return false;
        }

    }
}
