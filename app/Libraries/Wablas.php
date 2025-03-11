<?php
namespace App\Libraries;

use App\Helpers\Generate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Wablas
{

    public static function sendWhatsApp ($dto) {
        if (Wablas::checkConnection()['data']['status'] != 'connected') {
            return TapTalk::sendWhatsApp([
                'phone_number' => Generate::changeZeroto62($dto['phone_number']),
                'message' => $dto['message']
            ]);
        }
        $curl = curl_init();
        $token = env('WABLAS_TOKEN');
        $data = [
            'phone' => $dto['phone_number'],
            'message' => $dto['message'],
        ];

        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                "Authorization:" . $token,
            ),
        );
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, "https://". env('WABLAS_SERVER') .".wablas.com/api/send-message");
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result,true);
    }

    public static function checkConnection () {
        return json_decode(file_get_contents("https://".env('WABLAS_SERVER').".wablas.com/api/device/info?token=".env('WABLAS_TOKEN')), true);
    }

    public static function resetConnection () {
        return json_decode(file_get_contents("https://".env('WABLAS_SERVER').".wablas.com/api/device/reconnect?token=".env('WABLAS_TOKEN')), true);
    }

}
