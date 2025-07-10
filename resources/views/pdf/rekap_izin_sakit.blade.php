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
                <font color="#000000" style="font-size: 18px;"><b>LAPORAN IZIN SAKIT KARYAWAN</b></font>
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
                <th class="table-border text-center" style="padding: 8px;">Tanggal</th>
                <th class="table-border text-center" style="padding: 8px;">Keterangan</th>
                <th class="table-border text-center" style="padding: 8px;">Foto Bukti</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($izin_sakits as $index => $izin_sakit)
                <tr>
                    <td class="table-border text-center" style="padding: 6px;">{{ $index + 1 }}</td>
                    <td class="table-border text-left" style="padding: 6px;">{{ $izin_sakit->nama_karyawan }}</td>
                    <td class="table-border text-center" style="padding: 6px;">
                        {{ \Carbon\Carbon::parse($izin_sakit->tanggal)->locale('id')->translatedFormat('d/m/Y') }}
                    </td>
                    <td class="table-border text-left" style="padding: 6px;">{{ $izin_sakit->keterangan }}</td>
                    <td class="table-border text-center" style="padding: 6px;">
                        <img src="{{ $izin_sakit->foto_bukti }}" width="100px">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table cellspacing="0" border="0" width="100%" style="margin-top: 20px;">
        <tr>
            <td colspan=12 align="left" valign=middle>
                <font color="#000000"><br></font>
            </td>
        </tr>
        <tr>
            <td colspan=6 align="left" valign=middle>
                <font color="#000000"><br></font>
            </td>
            <td colspan=6 align="center" valign=middle>
                <font color="#000000">Bandung, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
                </font>
            </td>
        </tr>
        <tr>
            <td colspan=6 align="left" valign=middle>
                <font color="#000000"><br></font>
            </td>
            <td colspan=6 align="center" valign=middle>
                <font color="#000000">IT Manajer</font>
            </td>
        </tr>
        <tr>
            <td colspan=12 align="left" valign=middle>
                <font color="#000000"><br></font>
            </td>
        </tr>
        <tr>
            <td colspan=12 align="left" valign=middle>
                <font color="#000000"><br></font>
            </td>
        </tr>
        <tr>
            <td colspan=6 align="left" valign=middle>
                <font color="#000000"><br></font>
            </td>
            <td colspan=6 align="center" valign=middle>
                <font color="#000000">
                    @if (isset($manager_signature) && $manager_signature != null)
                        <img src="{{ $manager_signature }}" width="125px">
                    @else
                        <br><br><br>
                    @endif
                </font>
            </td>
        </tr>
        <tr>
            <td colspan=6 align="left" valign=middle>
                <font color="#000000"><br></font>
            </td>
            <td colspan=6 align="center" valign=middle>
                <font color="#000000">{{ auth()->user()->userInformation->nama ?? 'IT Manager' }}</font>
            </td>
        </tr>
    </table>
</body>

</html>
