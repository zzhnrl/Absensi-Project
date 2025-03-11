<?php
namespace App\Libraries;

use Illuminate\Support\Facades\Http;
use Pusher\Pusher as Broadcast;

class Pusher {
    protected $pusher, $channel_name, $event_name, $data;
    public function __construct($channel_name, $event_name, $data) {
        $this->channel_name = $channel_name;
        $this->event_name = $event_name;
        $this->data = $data;

        $this->pusher = new Broadcast(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );
    }

    public function execute () {
        $this->pusher->trigger(
            $this->channel_name,
            $this->event_name,
            $this->data
        );
    }

    public static function beams ($interest, $notification) {
        $url = "https://".env('BEAMS_INSTANCE_ID').".pushnotifications.pusher.com/publish_api/v1/instances/".env('BEAMS_INSTANCE_ID')."/publishes";
        $headers= [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('BEAMS_SECRET_KEY')
        ];
        $body = [
            "interests" => [$interest],
            "fcm" => [
                "notification" => $notification
            ]
        ];
        $response = Http::withHeaders($headers)->post($url, $body);

        $statusCode = $response->status();
        $responseBody = json_decode($response->getBody(), true);
        return response()->json($responseBody, $statusCode);

    }
}
