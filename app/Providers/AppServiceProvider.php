<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\AuthService\DoLogin;
use App\Services\AuthService\DoLogout;

use App\Services\User\GetUserService;
use App\Services\User\StoreUserService;
use App\Services\User\UpdateUserService;
use App\Services\User\DeleteUserService;

use App\Services\FileStorage\StoreFileStorageService;
use App\Services\FileStorage\DeleteFileStorageService;

use App\Services\Role\GetRoleService;
use App\Services\Role\StoreRoleService;
use App\Services\Role\UpdateRoleService;
use App\Services\Role\DeleteRoleService;

use App\Services\Permission\GetListPermissionModule;
use App\Services\Permission\UpdateRolePermission;

use App\Services\UserRole\AddUserRoleService;
use App\Services\UserRole\RemoveUserRoleService;

use App\Services\UserInformation\StoreUserInformationService;
use App\Services\UserInformation\GetUserInformationService;
use App\Services\UserInformation\DeleteUserInformationService;
use App\Services\UserInformation\UpdateUserInformationService;

use App\Services\StatusCuti\GetStatusCutiService;
use App\Services\StatusCuti\StoreStatusCutiService;
use App\Services\StatusCuti\UpdateStatusCutiService;
use App\Services\StatusCuti\DeleteStatusCutiService;

use App\Services\KategoriAbsensi\GetKategoriAbsensiService;
use App\Services\KategoriAbsensi\StoreKategoriAbsensiService;
use App\Services\KategoriAbsensi\UpdateKategoriAbsensiService;
use App\Services\KategoriAbsensi\DeleteKategoriAbsensiService;

use App\Services\Absensi\GetAbsensiService;
use App\Services\Absensi\StoreAbsensiService;
use App\Services\Absensi\DeleteAbsensiService;

use App\Services\PointUser\GetPointUserService;
use App\Services\PointUser\StorePointUserService;
use App\Services\PointUser\UpdatePointUserService;
use App\Services\PointUser\DeletePointUserService;

use App\Services\IzinSakit\GetIzinSakitService;
use App\Services\IzinSakit\StoreIzinSakitService;
use App\Services\IzinSakit\DeleteIzinSakitService;

use App\Services\RekapIzinSakit\GetRekapIzinSakitService;
use App\Services\RekapIzinSakit\StoreRekapIzinSakitService;
use App\Services\RekapIzinSakit\UpdateRekapIzinSakitService;

use App\Services\Cuti\GetCutiService;
use App\Services\Cuti\StoreCutiService;
use App\Services\Cuti\UpdateCutiService;

use App\Services\NotifikasiService\GetNotifikasi;
use App\Services\NotifikasiService\ReadNotifikasi;
use App\Services\NotifikasiService\StoreNotifikasi;

use App\Services\RegisterNewUserService;
use App\Services\EditUserService;
use App\Services\RemoveUserService;

use App\Services\Dashboard\GetDashboardJumlahKaryawanService;
use App\Services\Dashboard\GetDashboardJumlahCutiService;
use App\Services\Dashboard\SumAbsensiTotalByStatusService;
use App\Services\Dashboard\GetAbsensiDailyHistoryService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */

    public function register()
    {
        $this->registerService('RegisterNewUserService', RegisterNewUserService::class);
        $this->registerService('EditUserService', EditUserService::class);
        $this->registerService('RemoveUserService', RemoveUserService::class);

        $this->registerService('DoLogin', DoLogin::class);
        $this->registerService('DoLogout', DoLogout::class);

        $this->registerService('GetUserService', GetUserService::class);
        $this->registerService('StoreUserService', StoreUserService::class);
        $this->registerService('UpdateUserService', UpdateUserService::class);
        $this->registerService('DeleteUserService', DeleteUserService::class);

        $this->registerService('StoreFileStorageService', StoreFileStorageService::class);
        $this->registerService('DeleteFileStorageService', DeleteFileStorageService::class);

        $this->registerService('GetRoleService', GetRoleService::class);
        $this->registerService('StoreRoleService', StoreRoleService::class);
        $this->registerService('UpdateRoleService', UpdateRoleService::class);
        $this->registerService('DeleteRoleService', DeleteRoleService::class);

        $this->registerService('GetListPermissionModule', GetListPermissionModule::class);
        $this->registerService('UpdateRolePermission', UpdateRolePermission::class);

        $this->registerService('AddUserRoleService', AddUserRoleService::class);
        $this->registerService('RemoveUserRoleService', RemoveUserRoleService::class);

        $this->registerService('StoreUserInformationService', StoreUserInformationService::class);
        $this->registerService('GetUserInformationService', GetUserInformationService::class);
        $this->registerService('DeleteUserInformationService', DeleteUserInformationService::class);
        $this->registerService('UpdateUserInformationService', UpdateUserInformationService::class);

        $this->registerService('StoreStatusCutiService', StoreStatusCutiService::class);
        $this->registerService('GetStatusCutiService', GetStatusCutiService::class);
        $this->registerService('DeleteStatusCutiService', DeleteStatusCutiService::class);
        $this->registerService('UpdateStatusCutiService', UpdateStatusCutiService::class);

        $this->registerService('StoreKategoriAbsensiService', StoreKategoriAbsensiService::class);
        $this->registerService('GetKategoriAbsensiService', GetKategoriAbsensiService::class);
        $this->registerService('DeleteKategoriAbsensiService', DeleteKategoriAbsensiService::class);
        $this->registerService('UpdateKategoriAbsensiService', UpdateKategoriAbsensiService::class);

        $this->registerService('StoreAbsensiService', StoreAbsensiService::class);
        $this->registerService('GetAbsensiService', GetAbsensiService::class);
        $this->registerService('DeleteAbsensiService', DeleteAbsensiService::class);

        $this->registerService('StorePointUserService', StorePointUserService::class);
        $this->registerService('GetPointUserService', GetPointUserService::class);
        $this->registerService('DeletePointUserService', DeletePointUserService::class);
        $this->registerService('UpdatePointUserService', UpdatePointUserService::class);

        $this->registerService('StoreIzinSakitService', StoreIzinSakitService::class);
        $this->registerService('GetIzinSakitService', GetIzinSakitService::class);
        $this->registerService('DeleteIzinSakitService', DeleteIzinSakitService::class);

        $this->registerService('StoreRekapIzinSakitService', StoreRekapIzinSakitService::class);
        $this->registerService('GetRekapIzinSakitService', GetRekapIzinSakitService::class);
        $this->registerService('UpdateRekapIzinSakitService', UpdateRekapIzinSakitService::class);

        $this->registerService('StoreCutiService', StoreCutiService::class);
        $this->registerService('GetCutiService', GetCutiService::class);
        $this->registerService('UpdateCutiService', UpdateCutiService::class);

        $this->registerService('GetNotifikasi', GetNotifikasi::class);
        $this->registerService('ReadNotifikasi', ReadNotifikasi::class);
        $this->registerService('StoreNotifikasi', StoreNotifikasi::class);

        $this->registerService('GetDashboardJumlahKaryawanService', GetDashboardJumlahKaryawanService::class);
        $this->registerService('GetDashboardJumlahCutiService', GetDashboardJumlahCutiService::class);
        $this->registerService('SumAbsensiTotalByStatusService', SumAbsensiTotalByStatusService::class);
        $this->registerService('GetAbsensiDailyHistoryService', GetAbsensiDailyHistoryService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function registerService($serviceName, $className)
    {
        $this->app->singleton($serviceName, function () use ($className) {
            return new $className();
        });
    }
}
