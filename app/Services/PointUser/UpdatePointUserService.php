<?php

namespace App\Services\PointUser;

use App\Models\PointUser;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class UpdatePointUserService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $point_user = PointUser::find($dto['point_user_id']);

        if (!$point_user) {
            throw new \Exception("Point user not found");
        }

        $point_user->nama_karyawan = $dto['nama_karyawan'] ?? $point_user->nama_karyawan;
        $point_user->bulan = $dto['bulan'] ?? $point_user->bulan;
        $point_user->jumlah_point = $dto['jumlah_point'] ?? $point_user->jumlah_point;

        $this->prepareAuditUpdate($point_user);
        $point_user->save();

        $this->results['data'] = $point_user;
        $this->results['message'] = "Point user successfully updated";
    }

    public function prepare($dto)
    {
        if (isset($dto['point_user_uuid'])) {
            $dto['point_user_id'] = $this->findIdByUuid(PointUser::query(), $dto['point_user_uuid']);
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'point_user_uuid' => ['required', 'uuid', new ExistsUuid('point_users')],
            'nama_karyawan' => ['required'],
            'bulan' => ['nullable'],
            'jumlah_point' => ['required'],
        ];
    }
}
