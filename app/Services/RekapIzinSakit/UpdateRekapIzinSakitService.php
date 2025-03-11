<?php

namespace App\Services\RekapIzinSakit;

use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Models\RekapIzinSakit;
use App\Rules\ExistsUuid;

class UpdateRekapIzinSakitService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $rekap_izin_sakit = RekapIzinSakit::find($dto['rekap_izin_sakit_id']);

        if (!$rekap_izin_sakit) {
            throw new \Exception("rekap_izin_sakit not found");
        }

        $rekap_izin_sakit->jumlah_izin_sakit = $dto['jumlah_izin_sakit'] ?? $rekap_izin_sakit->jumlah_izin_sakit;

        $this->prepareAuditUpdate($rekap_izin_sakit);
        $rekap_izin_sakit->save();

        $this->results['data'] = $rekap_izin_sakit;
        $this->results['message'] = "Rekap izin sakit successfully updated";
    }

    public function prepare($dto)
    {
        if (isset($dto['rekap_izin_sakit_uuid'])) {
            $dto['rekap_izin_sakit_id'] = $this->findIdByUuid(RekapIzinSakit::query(), $dto['rekap_izin_sakit_uuid']);
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'rekap_izin_sakit_uuid' => ['required', 'uuid', new ExistsUuid('rekap_izin_sakits')],
            'jumlah_izin_sakit' => ['nullable']
        ];
    }
}
