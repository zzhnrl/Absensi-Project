<?php

namespace App\Services\PointUser;

use App\Models\PointUser;
use App\Models\User;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class StorePointUserService extends DefaultService implements ServiceInterface
{
    public function __construct() {}

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $point_user = new PointUser();

        $point_user->user_id = $dto['user_id'];
        $point_user->nama_karyawan = $dto['nama_karyawan'];
        $point_user->bulan = $dto['bulan'];
        $point_user->tahun = $dto['tahun'];
        $point_user->jumlah_point = $dto['jumlah_point'];

        $this->prepareAuditActive($point_user);
        $this->prepareAuditInsert($point_user);
        $point_user->save();

        $this->results['data'] = $point_user;
        $this->results['message'] = "Point user successfully created";
    }

    public function prepare($dto)
    {
        if (isset($dto['user_uuid'])) {
            $dto['user_id'] = $this->findIdByUuid(User::query(), $dto['user_uuid']);
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')],
            'nama_karyawan' => ['required'],
            'bulan' => ['required'],
            'tahun' => ['required'],
            'jumlah_point' => ['required'],
        ];
    }
}
