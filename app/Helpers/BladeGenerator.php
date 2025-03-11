<?php


function breadcrumb (Array $data) {
    $breadcrumb = '';
    foreach ($data as $row) {
        $breadcrumb .= "/ <a class='font-weight-bold' href='".$row['link']."'>".$row['name']."</a> ";
    }
    return $breadcrumb;
}

function each_option ($data,$row_value,$current_value,$for_filter = true, $index = 'uuid') {
    $string = ($for_filter) ? "<option value=''>-- Semua Data --</option>" : "";
    foreach ($data as $dt) {
        $row_idx = ($dt->$index ?? $dt[$index]);

        $string .= "\n<option value='". $row_idx ."'". (($current_value == $row_idx or (is_array($current_value) and in_array($row_idx,$current_value))) ? "selected" : "").">";
        if (is_callable($row_value)) {
            $string .= $row_value($dt);
        } else {
            $string .= ($dt->$row_value ?? $dt[$row_value]);;
        }
        $string .= "</option>";
    }
    return $string;
}

function generate_action_button ($data) {
    if (empty($data)) return '';
    $string = '<div class="dropdown">
    <button class="btn btn-default dropdown-toggle btn-sm" type="button" id="action-btn-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fas fa-edit"></i>
    </button>
    <div class="dropdown-menu" aria-labelledby="action-btn-dropdown" style="font-size:12px">';
    foreach ($data as $row) {
        $string .= $row;
    }
    $string .= '</div></div>';
    return $string;
}
