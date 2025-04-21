<!DOCTYPE html>
<html>
<head>
    <title>Pengajuan Cuti Baru</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }
        .content {
            padding: 20px;
            font-size: 16px;
            color: #333333;
            text-align: left;
        }
        .details {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            text-decoration: none;
            color: #ffffff;
            font-size: 16px;
            border-radius: 5px;
            background-color: #007bff;
            transition: background 0.3s;
            text-align: center;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #eeeeee;
            padding-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Pengajuan Cuti Baru</div>
        <div class="content">
            <p>Halo,</p>
            <p>Terdapat pengajuan cuti baru dengan detail sebagai berikut:</p>
            <div class="details">
                <p><strong>Nama:</strong> {{ $cutiData['nama_karyawan'] }}</p>
                <p><strong>Tanggal Mulai:</strong> {{ $cutiData['tanggal_mulai'] }}</p>
                <p><strong>Tanggal Akhir:</strong> {{ $cutiData['tanggal_akhir'] }}</p>
                <p><strong>Keterangan:</strong> {{ $cutiData['keterangan'] }}</p>
            </div>
            <p>Silakan cek sistem untuk informasi lebih lanjut.</p>

            <a href="{{ url('/cuti') }}" class="button" style="color: #ffffff;">Buka Halaman Approval</a>

        </div>
        <div class="footer">
            <p>© 2025 Perusahaan Anda | Email ini dikirim secara otomatis, mohon tidak membalas.</p>
        </div>
    </div>
</body>
</html>
