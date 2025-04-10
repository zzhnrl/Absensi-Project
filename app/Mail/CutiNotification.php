<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CutiNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $cutiData;

    public function __construct($cutiData)
    {
        $this->cutiData = $cutiData;
    }

    public function build()
    {
        return $this->subject('Pengajuan Cuti Baru')
                    ->view('emails.cuti_notification')
                    ->with('cutiData', $this->cutiData);
    }
}
