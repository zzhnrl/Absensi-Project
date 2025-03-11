<?php

namespace App\Traits;

use App\Helpers\DateTime;
use App\Helpers\Generate;
use Illuminate\Support\Facades\Auth;

trait Audit
{

    public function prepareAuditActive($object)
    {
        $object->{'is_active'} =  1;
    }

    public function prepareAuditNonActive($object)
    {
        $object->{'is_active'} =  0;
    }

    public function prepareAuditInsert($object)
    {
        $object->{'uuid'} = Generate::uuid();
        $object->{'created_at'} =  now()->timestamp;
        $object->{'updated_at'} =  now()->timestamp;
        $object->{'created_by'}  =  Auth::user()->id ?? null;
        $object->{'updated_by'} =  Auth::user()->id ?? null;
        $object->{'version'} = 0;
    }

    public function prepareAuditUpdate($object)
    {
        $object->{'updated_at'} =  now()->timestamp;
        $object->{'updated_by'} =  Auth::user()->id ?? null;
        $object->{'version'} = $object->{'version'} + 1;
    }

    public function prepareAuditRemove($object)
    {
        $object->{'deleted_at'} =  now()->timestamp;
        $object->{'deleted_by'} = Auth::user()->id ?? null;
    }

    public function prepareAuditRestore($object)
    {
        $object->{'deleted_at'} = null;
        $object->{'deleted_by'} = null;
    }


    public function activeAndRemoveData($object, $dto)
    {
        if (isset($dto['action']) && $dto['action'] ==  1) {
            if ($object->is_active == 1) {
                $message = "deactivated!";
                $this->prepareAuditNonActive($object);
            } else {
                $message = "activated!";
                $this->prepareAuditActive($object);
            }
        } else {
            if ($object->deleted_at == null) {
                $message = "removed!";
                $this->prepareAuditRemove($object);
                $this->prepareAuditNonActive($object);
            } else {
                $message = "recovered!";
                $this->prepareAuditRestore($object);
                $this->prepareAuditActive($object);
            }
        }

        $object->save();
        return $message;
    }

    // Audit untuk integrasi

    public function prepareAuditIntegrationInsert($object)
    {
        $object->{'uuid'} = Generate::uuid();
        $object->{'created_at'} =  now()->timestamp;
        $object->{'updated_at'} =  now()->timestamp;
        $object->{'created_by'} =  -98;
        $object->{'updated_by'} =  -98;
        $object->{'version'} = 0;
    }

    public function prepareAuditIntegrationUpdate($object)
    {
        $object->{'updated_at'} =  now()->timestamp;
        $object->{'updated_by'} =  -98;
        $object->{'version'} = $object->{'version'} + 1;
    }

    public function prepareAuditIntegrationRemove($object)
    {
        $object->{'deleted_at'} = now()->timestamp;
        $object->{'deleted_by'} = -98;
    }
}
