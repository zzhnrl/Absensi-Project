<?php
namespace App\Services\NotifikasiService;

use App\Models\DirectReward;
use App\Models\Notifikasi;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class ReadNotifikasi extends DefaultService implements ServiceInterface {

    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $notifikasi = Notifikasi::find($dto['notifikasi_id']);
        $notifikasi->is_read = 1;
        $this->prepareAuditUpdate($notifikasi);
        $notifikasi->save();

        $this->results['data'] = $notifikasi;
        $this->results['message'] = "Notifikasi successfully read";
    }

    public function prepare ($dto) {
        $dto['notifikasi_id'] = $this->findIdByUuid(Notifikasi::query(), $dto['notifikasi_uuid']);
       return $dto;
    }

    public function rules ($dto) {
        return [
            'notifikasi_uuid' => ['uuid','required', new ExistsUuid('notifikasi')],
        ];
    }

}
