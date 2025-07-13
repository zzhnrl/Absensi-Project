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
            font-size: 12px
        }

        a.comment-indicator:hover+comment {
            background: #ffd;
            position: absolute;
            display: block;
            border: 1px solid black;
            padding: 0.5em;
        }

        a.comment-indicator {
            background: red;
            display: inline-block;
            border: 1px solid black;
            width: 0.5em;
            height: 0.5em;
        }

        comment {
            display: none;
        }

        @page {
            margin: 1.27cm 1.04cm;
        }

        .page-break {
            page-break-after: always;
        }

        .main {
            width: 100%;
            margin-bottom: 20px;
        }

        .footer {
            bottom: 5px;
            width: 100%;
            height: 20px !important;
            position: absolute;
        }

        .left {
            float: left;
            display: inline-block;
            width: 50%;
        }

        .right {
            text-align: right;
            float: right;
            display: inline-block;
            width: 50%;
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
                <font color="#000000" style="font-size: 18px;"><b>LAPORAN ABSENSI KARYAWAN</b></font>
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
                <th class="table-border text-center" style="padding: 8px;">
                    <font color="#000000">No</font>
                </th>
                <th class="table-border text-center" style="padding: 8px;">
                    <font color="#000000">Nama Karyawan</font>
                </th>
                <th class="table-border text-center" style="padding: 8px;">
                    <font color="#000000">Jumlah WFO</font>
                </th>
                <th class="table-border text-center" style="padding: 8px;">
                    <font color="#000000">Jumlah WFH</font>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($absensi_rekap as $index => $absensi)
                <tr>
                    <td class="table-border text-center" style="padding: 6px;">
                        <font color="#000000">{{ $index + 1 }}</font>
                    </td>
                    <td class="table-border text-left" style="padding: 6px;">
                        <font color="#000000">{{ $absensi->nama_karyawan }}</font>
                    </td>
                    <td class="table-border text-center" style="padding: 6px;">
                        <font color="#000000">{{ $absensi->total_wfo ?? 0 }}</font>
                    </td>
                    <td class="table-border text-center" style="padding: 6px;">
                        <font color="#000000">{{ $absensi->total_wfh ?? 0 }}</font>
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
