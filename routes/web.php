<?php

use App\Exports\AbsensiExport;
use App\Exports\RekapAbsenExport;
use App\Exports\CutiExport;
use App\Exports\HistoryPointExport;
use App\Exports\RekapIzinSakitExport;
use App\Exports\UserExport;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\HistoryPointUserController;
use App\Http\Controllers\OfficeLocationController;
use App\Http\Controllers\RekapAbsenController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('test', function () {
    $data = app('GetAbsensiDailyHistoryService')->execute([
        'month' => 12,
        'year' => 2024
    ]);
    return $data;
});

Route::get('pdf', function () {
    $cuti = app('GetCutiService')->execute([
        'cuti_uuid' => 'b09d6a44-b930-11ef-b094-002b6706745f'
    ])['data'];

    $approval_by = (isset($cuti->approve_by)
    ? User::where('id', $cuti->approve_by)->first()->name 
    : User::where('id', $cuti->reject_by)->first()->name) ?? 'System';

    $pdf = PDF::loadView('pdf.cuti',[
        'cuti' => $cuti,
        'approval_by' => $approval_by
    ]);
    $file_name = "cuti" ;
    return $pdf->stream($file_name.".pdf");
});

Route::get('tdelete', function () {
    $data = app('DeleteUserService')->execute([
        'user_uuid' => 'abf2263a-860c-11ef-9079-04d4c4e101df',
    ]);
    return $data;
});

Route::get('rstore', function () {
    $data = app('StoreStatusCutiService')->execute([
        'nama' => 'Manager',
        'kode' => 'R02',
        'deskripsi' => 'Ini merupakan manager'
    ]);
    return $data;
});

Route::get('rget', function () {
    $data = app('GetStatusCutiService')->execute([]);
    return $data;
});

Route::get('rupdate', function () {
    $data = app('UpdateRoleService')->execute([
        'role_uuid' => '9602f1ba-8ada-11ef-a93a-04d4c4e101df',
        'name' => 'hihihi',
        'code' => 'R002',
        'description' => 'HIHIHI'
    ]);
    return $data;
});

Route::get('rdelete', function () {
    $data = app('DeleteRoleService')->execute([
        'role_uuid' => '9602f1ba-8ada-11ef-a93a-04d4c4e101df',
    ]);
    return $data;
});

Route::get('ustore', function () {
    $data = app('StoreIzinSakitService')->execute([
        'user_uuid' => 'ab2406c8-a629-11ef-a955-04d4c4e101df',
        'nama_karyawan' => 'Nama Satu',
        'tanggal' => '2024/12/08',
        'keterangan' => 'Ini merupakan alamat satu'
    ]);
    return $data;
});

Route::get('uget', function () {
    $data = app('GetRekapIzinSakitService')->execute([]);
    return $data;
});

Route::get('upa', function () {
    $data = app('StoreUserInformationService')->execute([
        'userinformation_uuid' => 'cce4d01c-8f77-11ef-b9b7-04d4c4e101df',
        'user_uuid' => 'e2624156-c977-41f2-94e7-24d0930614c7',
        'signature_file_id' => '1',
        'nama' => 'Nama dua',
        'notlp' => '081364792555',
        'alamat' => 'Ini merupakan alamat duaa',
    ]);
    return $data;
});

Route::get('udelete', function () {
    $data = app('DeleteUserInformationService')->execute([
        'userinformation_uuid' => 'a7d85950-9a63-11ef-84e7-04d4c4e101df',
    ]);
    return $data;
});

Route::get('tes', function () {
    $data = app('RegisterNewUserService')->execute([
        'email' => 'zizah@wangun.co',
        'password' => 'password',
        'role_uuid' => 'c4508389-9567-402d-9704-5ab43ae81f65',
        'nama' => 'Zizah',
        'notlp' => '081354325641',
        'alamat' => 'Subang'
    ]);
    return $data;
});

Route::group(['middleware' => ['auth']], function () {

    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('absensi-daily-history', [App\Http\Controllers\DashboardController::class, 'absensiDailyHistory']);
        Route::get('top-employee/grid', [App\Http\Controllers\DashboardController::class, 'topEmployeeGrid'])->name('dashboard.top-employee');
        Route::get('/jumlah-cuti/filter', [App\Http\Controllers\DashboardController::class, 'filterJumlahCuti'])->name('jumlah-cuti.filter');
    });

    Route::group(['prefix' => 'notifikasi'], function () {
        Route::get('', [App\Http\Controllers\NotificationController::class, 'index'])->name('notification');
        Route::put('read/{uuid}', [App\Http\Controllers\NotificationController::class, 'readNotification'])->name('notification.read');
        Route::get('list', [App\Http\Controllers\NotificationController::class, 'listNotification'])->name('notification.list');
        Route::get('grid', [App\Http\Controllers\NotificationController::class, 'grid'])->name('notification.grid');
    });

    Route::group(['prefix' => 'role'], function () {
        Route::group(['prefix' => '{uuid}/permission'], function () {
            Route::get('grid', [App\Http\Controllers\PermissionController::class, 'grid'])->name('role.permission.grid');
            Route::get('', [App\Http\Controllers\PermissionController::class, 'index'])->name('role.permission');
            Route::put('update-role', [App\Http\Controllers\PermissionController::class, 'updateRole'])->name('role.permission.update-role');
        });
        Route::get('grid', [App\Http\Controllers\RoleController::class, 'grid'])->name('role.grid');
    });

    Route::group(['prefix' => 'status_cuti'], function () {
        Route::get('grid', [App\Http\Controllers\StatusCutiController::class, 'grid'])->name('status_cuti.grid');
    });

    Route::group(['prefix' => 'kategori_absensi'], function () {
        Route::get('grid', [App\Http\Controllers\KategoriAbsensiController::class, 'grid'])->name('kategori_absensi.grid');
    });

    Route::group(['prefix' => 'absensi'], function () {
        Route::get('grid', [App\Http\Controllers\AbsensiController::class, 'grid'])->name('absensi.grid');
    });

    Route::group(['prefix' => 'rekap_absen'], function () {
        Route::get('grid', [App\Http\Controllers\RekapAbsenController::class, 'grid'])->name('rekap_absen.grid');
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('grid', [App\Http\Controllers\UserController::class, 'grid'])->name('user.grid');
    });

    Route::group(['prefix' => 'izin_sakit'], function () {
        Route::get('grid', [App\Http\Controllers\IzinSakitController::class, 'grid'])->name('izin_sakit.grid');
    });

    Route::group(['prefix' => 'cuti'], function () {
        Route::get('grid', [App\Http\Controllers\CutiController::class, 'grid'])->name('cuti.grid');
        Route::put('{cuti_uuid}/setujui', [App\Http\Controllers\CutiController::class, 'setujui'])->name('cuti.setujui');
        Route::put('{cuti_uuid}/tolak', [App\Http\Controllers\CutiController::class, 'tolak'])->name('cuti.tolak');
        Route::get('{cuti_uuid}/download', [App\Http\Controllers\CutiController::class, 'download'])->name('cuti.download');
    });

    Route::group(['prefix' => 'point_user'], function () {
        Route::get('grid', [App\Http\Controllers\PointUserController::class, 'grid'])->name('point_user.grid');
    });

    Route::group(['prefix' => 'rekap_izin_sakit'], function () {
        Route::get('grid', [App\Http\Controllers\RekapIzinSakitController::class, 'grid'])->name('rekap_izin_sakit.grid');
    });

    Route::resource('role', App\Http\Controllers\RoleController::class, [
        'names' => [
            'index' => 'role',
            'create' => 'role.create',
            'store' => 'role.store',
            'edit' => 'role.edit',
            'update' => 'role.update',
            'delete' => 'role.delete'
        ],
    ]);

Route::resource('user', App\Http\Controllers\UserController::class, [
    'names' => [
        'index' => 'user',
        'create' => 'user.create',
        'store' => 'user.store',
        'edit' => 'user.edit',
        'update' => 'user.update',
        'delete' => 'user.delete'
    ],
])->except(['show']);


    Route::resource('status_cuti', App\Http\Controllers\StatusCutiController::class, [
        'names' => [
            'index' => 'status_cuti',
            'create' => 'status_cuti.create',
            'store' => 'status_cuti.store',
            'edit' => 'status_cuti.edit',
            'update' => 'status_cuti.update',
            'delete' => 'status_cuti.delete'
        ],
    ]);

    Route::resource('kategori_absensi', App\Http\Controllers\KategoriAbsensiController::class, [
        'names' => [
            'index' => 'kategori_absensi',
            'create' => 'kategori_absensi.create',
            'store' => 'kategori_absensi.store',
            'edit' => 'kategori_absensi.edit',
            'update' => 'kategori_absensi.update',
            'delete' => 'kategori_absensi.delete'
        ],
    ]);

    Route::resource('absensi', App\Http\Controllers\AbsensiController::class, [
        'names' => [
            'index' => 'absensi',
            'create' => 'absensi.create',
            'store' => 'absensi.store',
            'delete' => 'absensi.delete'
        ],
    ]);

    Route::get('rekap-absen', [RekapAbsenController::class, 'index'])
    ->middleware('auth')
    ->name('rekap-absen.index');

// Endpoint JSON untuk DataTables
Route::get('rekap-absen/data', [RekapAbsenController::class, 'data'])
    ->middleware('auth')
    ->name('rekap-absen.data');

    Route::resource('point_user', App\Http\Controllers\PointUserController::class, [
        'names' => [
            'index' => 'point_user',
            'create' => 'point_user.create',
            'store' => 'point_user.store',
            'delete' => 'point_user.delete'
        ],
    ]);

    Route::resource('izin_sakit', App\Http\Controllers\IzinSakitController::class, [
        'names' => [
            'index' => 'izin_sakit',
            'create' => 'izin_sakit.create',
            'store' => 'izin_sakit.store',
            'delete' => 'izin_sakit.delete'
        ],
    ]);

    Route::resource('rekap_izin_sakit', App\Http\Controllers\RekapIzinSakitController::class, [
        'names' => [
            'index' => 'rekap_izin_sakit',
            'create' => 'rekap_izin_sakit.create',
            'store' => 'rekap_izin_sakit.store',
            'delete' => 'rekap_izin_sakit.delete',
            'update' => 'rekap_izin_sakit.update'
        ],
    ]);

    Route::resource('cuti', App\Http\Controllers\CutiController::class, [
        'names' => [
            'index' => 'cuti',
            'create' => 'cuti.create',
            'store' => 'cuti.store',
            'delete' => 'cuti.delete',
            'setujui' => 'cuti.setujui',
            'tolak' => 'cuti.tolak',
            'download' => 'cuti.download'
        ],
    ]);

    Route::resource('profile', App\Http\Controllers\ProfileController::class, [
        'names' => [
            'index' => 'profile',
            'update' => 'profile.update',
        ],
    ]);
});



Route::get('/input-lokasi', [OfficeLocationController::class, 'inputLokasi'])->name('inputLokasi');
Route::post('/set-office-location', [OfficeLocationController::class, 'store'])->name('setOfficeLocation');

Route::get('/cek-kehadiran', [OfficeLocationController::class, 'cekKehadiran'])->name('cekKehadiran');

Route::get('/get-office-location', [OfficeLocationController::class, 'getOfficeLocation']);

Route::get('/get-sisa-cuti/{id}', [UserController::class, 'getSisaCuti']);


Route::get('/cuti/export/excel', function () {
    return Excel::download(new CutiExport, 'data-cuti.xlsx');
})->name('cuti.export.excel');

// Route::get('/absensi/export/excel', function () {
//     return Excel::download(new AbsensiExport, 'absensi.xlsx');
// })->name('absensi.export.excel');
Route::get('/absensi/export/pdf', [AbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');


Route::get('/user/export', function () {
    return Excel::download(new UserExport, 'data_user.xlsx');
})->name('user.export');


// Route::get('/rekap-izin-sakit/export', function () {
//     return Excel::download(new RekapIzinSakitExport, 'rekap_izin_sakit.xlsx');
// })->name('rekap_izin_sakit.export');
Route::get('/rekap-izin-sakit/export/pdf', function () {
    $izin_sakits = \App\Models\IzinSakit::whereNull('deleted_at')->get();
    
    // Get manager signature if available
    $manager_signature = null;
    if (auth()->check() && auth()->user()->userInformation) {
        $manager_signature = auth()->user()->userInformation->signatureFile->url ?? null;
    }
    
    $pdf = PDF::loadView('pdf.rekap_izin_sakit', [
        'izin_sakits' => $izin_sakits,
        'manager_signature' => $manager_signature
    ]);
    $file_name = "Laporan_Izin_Sakit_" . date('Y-m-d_H-i-s');
    return $pdf->stream($file_name . ".pdf");
})->name('rekap-izin-sakit.export.pdf');

Route::get('/rekap-absen/export/excel', function () {
    return Excel::download(new RekapAbsenExport, 'rekap_absen.xlsx');
})->name('rekap-absen.export.excel');

Route::get('/cuti/export/pdf', function () {
    $cutis = \App\Models\Cuti::with(['statusCuti', 'approveByUser.userInformation', 'rejectByUser.userInformation'])
        ->whereNull('deleted_at')
        ->get();

    $pdf = PDF::loadView('pdf.cuti_report', [
        'cutis' => $cutis
    ]);
    $file_name = "Laporan_Cuti_" . date('Y-m-d_H-i-s');
    return $pdf->stream($file_name . ".pdf");
})->name('cuti.export.pdf');


Route::middleware('auth')->group(function () {
    Route::get('/history-point', [HistoryPointUserController::class, 'index'])
         ->name('history-point.index');

    Route::get('/history-point/grid', [HistoryPointUserController::class, 'grid'])
         ->name('history-point.grid');
});



// routes/web.php
Route::post('/cuti/hitung', [CutiController::class, 'hitungCuti'])->name('cuti.hitung');

Route::get('/export-history-point', function () {
    $month = request('month');
    $year = request('year');

    return Excel::download(new HistoryPointExport($month, $year), 'history-point.xlsx');
})->name('history-point.export');

