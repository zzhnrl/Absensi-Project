<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
    <style type="text/css">
        body,div,table,thead,tbody,tfoot,tr,th,td,p { font-family:"Calibri"; font-size:large }
        a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  }
        a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  }
        comment { display:none;  }
        @page {
            margin:1.27cm 1.04cm;
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
            display:inline-block;
            width:50%;
        }
        .right {
            text-align: right;
            float: right;
            display:inline-block;
            width:50%;
        }
    </style>

</head>

<body>
    <table cellspacing="0" border="0" width="100%">
        <tr>
			<td colspan=3 rowspan=2 align="center" valign=middle><font color="#000000"><img src="{{ public_path('img/logo/logo-tw.png') }}" width="50%" alt=""></font></td>
			<td style="font-size: 32px" colspan=6 align="center" valign=middle><b><font color="#000000">PT. Indiga Nusa Digitama</font></b></td>
        </tr>
        <tr>
			<td colspan=6 align="center" valign=middle><font color="#000000">Jl. Belimbing, No. 13, Cihapit, Kec. Bandung Wetan, Kota Bandung, Jawa Barat, 40114</font></td>
        </tr>
        <tr>
			<td colspan=9 style="border-bottom: 1px double #000000;" align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td colspan=9 align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td colspan=9 align="left" valign=middle><font color="#000000">Saya yang bertanda tangan di bawah ini :</font></td>
        </tr>
        <tr>
			<td align="left" valign=middle><font color="#000000"><br></font></td>
			<td align="left" valign=middle><font color="#000000">Nama</font></td>
			<td colspan=7 align="left" valign=middle><font color="#000000">: {{ $cuti->nama_karyawan }}</font></td>
        </tr>
        <tr>
			<td colspan=9 align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td colspan=9 align="left" valign=middle><font color="#000000">Mengajukan permohonan cuti kerja :</font></td>
        </tr>
        <tr>
			<td align="left" valign=middle><font color="#000000"><br></font></td>
			<td align="left" valign=middle><font color="#000000">Tanggal</font></td>
            <td colspan=7 align="left" valign="middle">
                <font color="#000000">
                    : {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->locale('id')->translatedFormat('d F Y') }} s/d 
                    {{ \Carbon\Carbon::parse($cuti->tanggal_akhir)->locale('id')->translatedFormat('d F Y') }}
                </font>
            </td>
        </tr>
        <tr>
			<td align="left" valign=middle><font color="#000000"><br></font></td>
			<td align="left" valign=middle><font color="#000000">Keterangan</font></td>
			<td colspan=7 align="left" valign=middle><font color="#000000">: {{ $cuti->keterangan }}</font></td>
        </tr>
        <tr>
			<td colspan=9 align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td colspan=9 align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td colspan=9 align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td colspan=6 align="left" valign=middle><font color="#000000"><br></font></td>
			<td colspan=3 align="center" valign=middle><font color="#000000">Bandung, {{ \Carbon\Carbon::parse($cuti->approve_at)->locale('id')->translatedFormat('d F Y') }}</font></td>
        </tr>
        <tr>
			<td colspan=9 align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td align="left" valign=middle><font color="#000000"><br></font></td>
			<td colspan=3 align="center" valign=middle><font color="#000000">Hormat Saya</font></td>
			<td align="left" valign=middle><font color="#000000"><br></font></td>
			<td colspan=3 align="center" valign=middle><font color="#000000">IT Manajer</font></td>
			<td align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td colspan=9 align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td align="left" valign=middle><font color="#000000"><br></font></td>  
			<td colspan=3 align="center" valign=middle><font color="#000000">
                @if ($user_tanda_tangan != null)
                    <img src="{{ $user_tanda_tangan }}" width="125px"></font>
                @else
                    <br>
                @endif
            </td>
			<td align="left" valign=middle><font color="#000000"><br></font></td>
			<td colspan=3 align="center" valign=middle><font color="#000000">
                @if ($manager_tanda_tangan != null)
                    <img src="{{ $manager_tanda_tangan }}" width="125px"></font>
                @else
                    <br>
                @endif
            </td>
			<td align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td colspan=9 align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
        <tr>
			<td align="left" valign=middle><font color="#000000"><br></font></td>  
			<td colspan=3 align="center" valign=middle><font color="#000000">{{ $cuti->nama_karyawan }}</font></td>
			<td align="left" valign=middle><font color="#000000"><br></font></td>
			<td colspan=3 align="center" valign=middle><font color="#000000">{{ $approval_by }}</font></td>
			<td align="left" valign=middle><font color="#000000"><br></font></td>
        </tr>
    </table>
    <!-- ************************************************************************** -->
</body>

</html>
