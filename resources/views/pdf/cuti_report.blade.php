<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>

<head>
    <style type="text/css">
        body,
        div,
        table,
        thead,
        tbody,
        tfoot,
        tr,
        th,
        td,
        p {
            font-family: "Calibri";
            font-size: 12px;
        }

        @page {
            margin: 1.27cm 1.04cm;
        }

        .table-header {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .table-border {
            border: 1px solid #000000;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <table cellspacing="0" border="0" width="100%">
        <tr>
            <td colspan=6 rowspan=2 align="center" valign=middle>
                <font color="#000000"><img src="{{ public_path('img/logo/logo-tw.png') }}" width="50%" alt="">
                </font>
            </td>
            <td style="font-size: 32px" colspan=6 align="center" valign=middle><b>
                    <font color="#000000">PT. Indiga Nusa Digitama</font>
                </b></td>
        </tr>
        <tr>
            <td colspan=6 align="center" valign=middle>
                <font color="#000000">Jl. Belimbing, No. 13, Cihapit, Kec. Bandung Wetan, Kota Bandung, Jawa Barat,
                    40114</font>
            </td>
        </tr>
        <tr>
            <td colspan=12 style="border-bottom: 1px double #000000;" align="left" valign=middle>
                <font color="#000000"><br></font>
            </td>
        </tr>
        <tr>
            <td colspan=12 align="left" valign=middle>
                <font color="#000000"><br></font>
            </td>
        </tr>
        <tr>
            <td colspan=12 align="center" valign=middle>
                <font color="#000000" style="font-size: 18px;"><b>LAPORAN CUTI KARYAWAN</b></font>
            </td>
        </tr>
        <tr>
            <td colspan=12 align="center" valign=middle>
                <font color="#000000">Periode: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
                </font>
            </td>
        </tr>
        <tr>
            <td colspan=12 align="left" valign=middle>
                <font color="#000000"><br></font>
            </td>
        </tr>
    </table>

    <table cellspacing="0" border="1" width="100%" style="border-collapse: collapse;">
        <thead>
            <tr class="table-header">
                <th class="table-border text-center" style="padding: 8px;">No</th>
                <th class="table-border text-center" style="padding: 8px;">Nama Karyawan</th>
                <th class="table-border text-center" style="padding: 8px;">Tanggal Mulai</th>
                <th class="table-border text-center" style="padding: 8px;">Tanggal Akhir</th>
                <th class="table-border text-center" style="padding: 8px;">Lama Cuti</th>
                <th class="table-border text-center" style="padding: 8px;">Jenis Cuti</th>
                <th class="table-border text-center" style="padding: 8px;">Keterangan</th>
                <th class="table-border text-center" style="padding: 8px;">Status</th>
                <th class="table-border text-center" style="padding: 8px;">Pemberi Keputusan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cutis as $index => $cuti)
                <tr>
                    <td class="table-border text-center" style="padding: 6px;">{{ $index + 1 }}</td>
                    <td class="table-border text-left" style="padding: 6px;">{{ $cuti->nama_karyawan }}</td>
                    <td class="table-border text-center" style="padding: 6px;">
                        {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->locale('id')->translatedFormat('d/m/Y') }}</td>
                    <td class="table-border text-center" style="padding: 6px;">
                        {{ \Carbon\Carbon::parse($cuti->tanggal_akhir)->locale('id')->translatedFormat('d/m/Y') }}</td>
                    <td class="table-border text-center" style="padding: 6px;">
                        {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_akhir)) }}
                    </td>
                    <td class="table-border text-center" style="padding: 6px;">{{ $cuti->jenis_cuti }}</td>
                    <td class="table-border text-left" style="padding: 6px;">{{ $cuti->keterangan }}</td>
                    <td class="table-border text-center" style="padding: 6px;">{{ $cuti->statusCuti->nama ?? '-' }}</td>
                    <td class="table-border text-left" style="padding: 6px;">
                        @if ($cuti->status_cuti_id == 2)
                            {{ optional($cuti->approveByUser)->userInformation->nama ?? '-' }}
                        @elseif($cuti->status_cuti_id == 3)
                            {{ optional($cuti->rejectByUser)->userInformation->nama ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table cellspacing="0" border="0" width="100%" style="margin-top: 20px;">
        <tr>
            <td colspan=8>&nbsp;</td>
        </tr>
        <tr>
            <td colspan=4></td>
            <td colspan=4 align="center">Bandung, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td colspan=4></td>
            <td colspan=4 align="center">IT Manajer</td>
        </tr>
        <tr>
            <td colspan=8>&nbsp;</td>
        </tr>
        <tr>
            <td colspan=8>&nbsp;</td>
        </tr>
        <tr>
            <td colspan=4></td>
            <td colspan=4 align="center">{{ auth()->user()->userInformation->nama ?? 'IT Manager' }}</td>
        </tr>
    </table>
</body>

</html>
