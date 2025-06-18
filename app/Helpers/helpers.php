<?php

if (!function_exists('each_option')) {
    /**
     * Generate <option> HTML tags from array or collection
     */
    function each_option($data, $labelKey, $selected = null)
    {
        $html = '';

        foreach ($data as $key => $item) {
            $value = is_array($item) ? $item[$labelKey] : $item->$labelKey;
            $itemKey = is_array($item) ? $item['id'] ?? $key : ($item->id ?? $key);
            $isSelected = ($itemKey == $selected) ? 'selected' : '';
            $html .= "<option value=\"{$itemKey}\" {$isSelected}>{$value}</option>";
        }

        return $html;
    }
}
