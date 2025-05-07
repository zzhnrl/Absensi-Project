<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifikasi Izin Sakit Karyawan</title>
    <style>
        body {
            background-color: #f2f4f8;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #444;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            width: 100%;
            padding: 40px 0;
        }

        .email-content {
            background: #ffffff;
            max-width: 600px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .email-header {
            background-color: #1a73e8;
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .email-header h2 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .email-body {
            padding: 30px;
        }

        .email-body p {
            font-size: 15px;
            line-height: 1.6;
            margin: 15px 0;
        }

        .label {
            font-weight: bold;
            color: #333;
        }

        .email-footer {
            padding: 20px 30px;
            text-align: center;
            font-size: 13px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                <h2>📢 Izin Sakit Karyawan</h2>
            </div>
            <div class="email-body">
                <p><span class="label">Nama Karyawan:</span> {{ $karyawan }}</p>
                <p><span class="label">Tanggal Pengajuan:</span> {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p>
                <p><span class="label">Keterangan:</span> {{ $keterangan }}</p>

                <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

                <p>Mohon untuk segera ditindaklanjuti oleh pihak yang berwenang.</p>
                <p>Terima kasih.</p>
            </div>
            <div class="email-footer">
                &copy; {{ date('Y') }} Sistem Informasi HR | Email ini dikirim otomatis, mohon tidak dibalas.
            </div>
        </div>
    </div>
</body>
</html>
