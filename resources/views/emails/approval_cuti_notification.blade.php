<!DOCTYPE html>
<html>
<head>
    <title>Notifikasi Cuti</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
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
            text-align: center;
        }
        .header {
            background-color: #3498db;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
        }
        .status {
            font-size: 18px;
            font-weight: bold;
            color: {{ $status == 'disetujui' ? '#27ae60' : '#e74c3c' }};
        }
        .content {
            padding: 20px;
            font-size: 16px;
            color: #333333;
            text-align: left;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            text-decoration: none;
            color: #ffffff;
            font-size: 16px;
            border-radius: 5px;
            background-color: #3498db;
            transition: background 0.3s;
        }
        .button:hover {
            background-color: #2980b9;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #eeeeee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Notifikasi Hati <strong>{{ $approvedBy }}</strong></div>
        <div class="content">
            <p>Halo <strong>{{ $namaKaryawan }}</strong>,</p>
            <p>Permohonan <strong>{{ $namaKaryawan }}</strong> untuk masuk ke hati <strong>{{ $approvedBy }}</strong> telah <span class="status">{{ ucfirst($status) }}</span> oleh <strong>{{ $approvedBy }}</strong>.</p>
            <p>Silakan cek Hati <strong>{{ $namaKaryawan }}</strong> untuk detail lebih lanjut.</p>
        </div>
        <div class="footer">
            <p>I LOVE YOU FOREVERR <strong>{{ $namaKaryawan }}</strong></p>
            <p>© 2025 Hati Yoga | Email ini dikirim secara otomatis, mohon membalas dengan baik hati.</p>
        </div>
    </div>
</body>
</html>