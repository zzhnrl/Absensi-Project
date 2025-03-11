<?php

namespace App\Traits;

trait Identifier
{

    public function findIdByUuid($object, $uuid)
    {
        $results = $object->where('uuid', $uuid)->where('is_active', 1)->first();
        return !empty($results->id) ? $results->id : null;
    }

    public function findUuidById($object, $id)
    {
        $results = $object->where('id', $id)->where('is_active', 1)->first();
        return !empty($results->uuid) ? $results->uuid : null;
    }

    public function findIdByUsername($object, $username)
    {
        $results = $object->where('username', $username)->where('is_active', 1)->first();
        return !empty($results->id) ? $results->id : null;
    }

    public function findIdByCode($object, $code)
    {
        $results = $object->where('code', $code)->where('is_active', 1)->first();
        return !empty($results->id) ? $results->id : null;
    }
}
