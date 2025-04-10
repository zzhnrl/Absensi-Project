<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IzinSakitNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $karyawan;
    public $tanggal;
    public $keterangan;

    public function __construct($karyawan, $tanggal, $keterangan)
    {
        $this->karyawan = $karyawan;
        $this->tanggal = $tanggal;
        $this->keterangan = $keterangan;
    }

    public function build()
    {
        return $this->subject('Pengajuan Izin Sakit')
            ->view('emails.izin_sakit_notification');
    }
}
