<?php
namespace App\Services\NotifikasiService;

use App\Models\DirectReward;
use App\Models\Notifikasi;
use App\Models\User;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class StoreNotifikasi extends DefaultService implements ServiceInterface {

    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $notifikasi =  new Notifikasi;

        $notifikasi->mstr_user_id = $dto['mstr_user_id'];
        $notifikasi->judul = $dto['judul'];
        $notifikasi->relation_key = $dto['relation_key'];
        $notifikasi->teks = $dto['teks'];
        $notifikasi->url = $dto['url'] ?? null;
        $notifikasi->is_read = 0;
        $notifikasi->waktu_notifikasi = $dto['waktu_notifikasi'];

        $this->prepareAuditActive($notifikasi);
        $this->prepareAuditInsert($notifikasi);
        $notifikasi->save();

        $this->results['data'] = [];
        $this->results['message'] = "Notifikasi successfully stored";
    }

    public function prepare ($dto) {
        $dto['mstr_user_id'] = $this->findIdByUuid(User::query(), $dto['mstr_user_uuid']);
       return $dto;
    }

    public function rules ($dto) {
        return [
            'mstr_user_uuid' => ['nullable', 'uuid', new ExistsUuid('mstr_user')],
            'relation_key' => ['required'],
            'judul' => ['required'],
            'teks' => ['required'],
            'url' => ['nullable'],
            'waktu_notifikasi' => ['required']
        ];
    }

}
