<?php
require_once '../database.php';
$conn = new Database();

if ($_SESSION['rights'] === '1') {
    $result = $conn->Select('employee');

    $columns = $conn->getTableColumns('employee');
    $table = '<div class="table"> <table> ';
    $table .= '<tr>';
    foreach ($columns as $name) {
        $table .= '<th>' . $name . '</th>';
    }
    $table .= '</tr>';
    foreach ($result[0] as $record) {
        $table .= '<tr data-id=' . $record[0] . (isset($_GET['param']) && $_GET['param'] == $record[0] ? ' class="selected"' : '') . '>';

        $i = 0;
        unset($record[0]);
        foreach ($record as $value) {
            $table .= '<td>' . $value . '</td>';
            $i++;
        }
        $table .= '</tr>';

    }
    $table .= '</table> ';
    $table .= '<div class="editBlock">';

        $table .= '<div class="edit" id="edit">Edit</div>
                    <div class="delete" id="delete">Delete</div>
                    <div class="add" id="addit">Add</div>
                    <div class="save" id="save" style="display: none">Save</div>';

    $table .= '</div></div>';
    if ($i) {
        $table .= '<table id="additional">';
        for ($i = 0; $i < count($columns); $i++) {
            $table .= '<td><input class="shown" placeholder="' . $columns[$i] . '"></td>';
        }
        $table .= '</table><div class="save" id="saveAdd" >Save</div>';
    }
}