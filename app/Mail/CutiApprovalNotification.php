<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CutiApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $status;
    public $namaKaryawan;
    public $approvedBy;

    public function __construct($status, $namaKaryawan, $approvedBy)
    {
        $this->status = $status;
        $this->namaKaryawan = $namaKaryawan;
        $this->approvedBy = $approvedBy;
    }

    public function build()
    {
        $subject = "Cuti" . ucfirst($this->status);

        return $this->subject($subject)
                    ->view('emails.approval_cuti_notification')
                    ->with([
                        'status' => $this->status,
                        'namaKaryawan' => $this->namaKaryawan,
                        'approvedBy' => $this->approvedBy
                    ]);
    }
}


