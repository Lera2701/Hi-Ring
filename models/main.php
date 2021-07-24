<?php
require_once '../database.php';
$role = '';
switch ($_SESSION['rights']) {
    case '0':
        $role = 'undefined';
        break;
    case '1':
        $role = 'admin';
        break;
    case '2':
        $role = 'companyDirector';
        break;
    case '3':
        $role = 'firmDirector';
        break;
    case '4':
        $role = 'companyResponsible';
        break;
    case '5':
        $role = 'firmWorker';
        break;
}

$conn = new Database();
$result = $conn->Select('schedule');
$columns = $conn->getTableColumns('schedule');
$table = '<table>';
$table .= '<tr>';
foreach ($columns as $name) {
    $table .= '<th>' . $name . '</th>';
}
$table .= '</tr>';
foreach ($result[0] as $record) {
    $table .= '<tr>';

    foreach ($record as $value) {
        $table .= '<td>' . $value . '</td>';

    }
    $table .= '</tr>';

}
$table .= '</table>';
//echo($table);
