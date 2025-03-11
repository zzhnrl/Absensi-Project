<?php

namespace App\Traits;

trait Pagination
{

    public function paginateData($object, $perPage, $page, $isModel = true)
    {
        if ($page < 1) {
            $page = 1;
        }
        if ($isModel) {
            return $object->skip($perPage * ($page - 1))->take($perPage);
        } else {
            $res = [];
            foreach ($object->skip($perPage * ($page - 1))->take($perPage) as $key => $value) {
                $res[] = $value;
            }

            return $data = collect($res);
        }
    }

    public function paginationDetail($perPage, $page, $count)
    {
        return [
            'data_per_page' => (int) $perPage,
            'next_page' => (int) $page + 1,
            'prev_page' => (int) $page - 1,
            'first_page' => 1,
            'last_page' => (int) number_format($count / $perPage, 0),
            'next_page_url' => url()->current() . "?per_page=" . $perPage . "&page_number=" . ($page + 1),
            'previous_page_url' => url()->current() . "?per_page=" . $perPage . "&page_number=" . ($page - 1),
            'first_page_url' => url()->current() . "?per_page=" . $perPage . "&page_number=1",
            'last_page_url' => url()->current()
                . "?per_page="
                . $perPage
                . "&page_number="
                . (number_format($count / $perPage, 0)),
            'total_page' => (int) ceil($count / $perPage),
            'total_data' => (int) $count
        ];
    }
}
