<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Libraries\Wablas;
use Carbon\Carbon;

class SendWA implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $phone_number, $message;

    public function __construct($data)
    {
        $this->phone_number = $data['phone_number'];
        $this->message = $data['message'];
    }

    /**
     * Execute the job.
     *.
     * @return voideb
     */
    public function handle()
    {
        Wablas::sendWhatsApp([
            'phone_number' => $this->phone_number,
            'message' => $this->message,
        ],false);
    }
}
