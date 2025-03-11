<?php
namespace App\Libraries;
use GuzzleHttp\Client;

class TapTalk
{

    public static function sendWhatsApp ($dto) {

        $url = "https://sendtalk-api.taptalk.io/api/v1/message/send_whatsapp";
        $data = [
            'phone' => $dto['phone_number'],
            "messageType" => "otp",
            'body' => $dto['message'],
        ];

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'API-Key' => env('TAPTALK_KEY')
                ]
            ],
        );

        $response = $client->post($url,['body' => json_encode($data)]);
        return $response;

    }


}
