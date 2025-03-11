<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsData implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $table;


    public function __construct($table, $cols = null)
    {
        $this->table = $table;
        $this->cols = $cols;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $query = DB::table($this->table)->where($this->cols, $value)->where('deleted_at', null);
        $result = !empty($query->first()) ? true : false;
        return $result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Nomor Handphone tidak ditemukan.';
    }
}
