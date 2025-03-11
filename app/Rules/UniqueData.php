<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueData implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $uuid;

    protected $table;

    protected $column;

    public function __construct($table, $column, $uuid = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->uuid = $uuid;
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
        $query = DB::table($this->table)->where($this->column, $value)->where('deleted_at', null);
        $this->uuid != null ? $query->where('uuid', '!=', $this->uuid) : null;
        $result = empty($query->first()) ? true : false;
        return $result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Atribut :attribute sudah digunakan.';
    }
}
