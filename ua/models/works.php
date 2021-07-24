<?php
require_once '../../database.php';
$conn = new Database();
$sql = "select #SELECT#d.Name as 'Company', c.Name as 'Location', a.StartTime as 'Start', a.EndTime as 'End', b.Responsible, f.Name as 'Worker' from work a, work_details b, location c, company d, employee f where a.ID = b.Work and c.Company = d.ID and c.ID = a.Location and f.ID = b.Worker #AND# order by a.StartTime desc";
$columns = $conn->getTableColumns('schedule');
$result = '';
$companies = [];
foreach ($conn->Select('company', [0=>'name', 1=>'id'])[0] as $com) {
    $companies[$com[1]] = ['name'=>$com[0]];
}
$locations = [];
foreach($conn->Select('location', [0=>'name', 1=>'id', 2=>'company'])[0] as $loc) {
    $locations[$loc[2]][] = ['id'=>$loc[1], 'name'=>$loc[0]];
}
foreach ($locations as $key=>$loc) {
    foreach ($loc as $l) {
        $companies[$key][] = ['locid'=>$l['id'],'locname'=>$l['name']];
    }
}
$responsibles = [];
foreach($conn->Select('employee', [0=>'name', 1=>'id'], null, '`rights`="4"')[0] as $emp) {
    $responsibles[$emp[0]] = ['id'=>$emp[1]];
}
$workers = [];
foreach($conn->Select('employee', [0=>'name', 1=>'id'], null, '`rights`="5"')[0] as $emp) {
    $workers[$emp[0]] = ['id'=>$emp[1]];
}//print_r($workers);
$dropdown = '<select class="dropdown">
   <option value=""></option>
   <option value="new">Новий запис</option>';
if (isset($_GET['dropdown'])) {
    switch ($_GET['dropdown']) {
        case 'Company':
            foreach ($companies as $key=>$company) {
                if (array_key_exists('name', $company))
                    $dropdown .= '<option value="'.$key.'">'.$company['name'].'</option>';
            }
            break;
        case 'Location':
            $key = 0;
            foreach ($companies as $key1=>$company) {
                if (array_search($_GET['company'], $company) !== false){
                    $key = $key1;
                }
            }
            if ($key !== 0)
                $dropdown .= '<option value="'.$companies[$key][0]['locid'].'">'.$companies[$key][0]['locname'].'</option>';
            break;
        case 'Responsible':
            foreach ($responsibles as $key=>$responsible) {
                $dropdown .= '<option value="'.$responsible['id'].'">'.$key.'</option>';
            }
            break;
        case 'Worker':
            foreach ($workers as $key=>$worker) {
                $dropdown .= '<option value="'.$worker['id'].'">'.$key.'</option>';
            }
            break;
    }
    $dropdown .= '</select>';
    echo json_encode($dropdown);
}

if ($_SESSION['rights'] === '1') {
    //$result = $conn->Select('schedule');
    $query = str_replace('#AND#', "", $sql);
    $query = str_replace('#SELECT#', "a.ID as ID, b.ID as ID, ", $query);
    $result = $conn->Query($query);
    $table = '<div class="table_workers"> <table> ';
    $table .= '<tr>';
    foreach ($columns as $name) {
        $table .= '<th>' . $name . '</th>';
    }
    $table .= '</tr>';

    foreach ($result[0] as $record) {
        $i = 0;
        $table .= '<tr data-id=' . $record[0] . ' data-detail='. $record[1] . (isset($_GET['param']) && $_GET['param'] == $record[0] ? ' class="selected"' : '') . '>';
        unset($record[0], $record[1]);
        foreach ($record as $value) {
            $table .= '<td data-id='.$columns[$i].'>' . $value . '</td>';
            $i++;
        }
        $table .= '</tr>';

    }
    $table .= '</table> ';
    $table .= '<div class="editBlock">';

    $table .= '<div class="edit" id="edit_works">Редагувати</div>
                    <div class="delete" id="delete_works">Видалити</div>
                    <div class="add" id="addit_works">Додати</div>
                    <div class="save" id="save_works" style="display: none">Зберегти</div>';

    $table .= '</div></div>';
    if ($i) {
        $table .= '<table id="additional_works">';
        for ($i = 0; $i < count($columns); $i++) {
            $table .= '<td><input class="shown" placeholder="' . $columns[$i] . '"></td>';
        }
        $table .= '</table><div class="save" id="saveAdd_works" >Зберегти</div>';
    }
} else if($_SESSION['rights'] === '2') {
    $firm = $conn->Select('employee', 'Firm', 1, '`ID`=' . $_SESSION['id']);
    $query = str_replace('#AND#', " and d.Name = '" . $firm[0] . "'", $sql);
    $query = str_replace('#SELECT#', "", $query);
    $result = $conn->Query($query);
} else if($_SESSION['rights'] === '3') {
    $firm = $conn->Select('employee', 'Firm', 1, '`ID`=' . $_SESSION['id']);
    $query = str_replace('#AND#', " and f.Firm = '" . $firm[0] . "'", $sql);
    $query = str_replace('#SELECT#', "", $query);
    $result = $conn->Query($query);
} else if($_SESSION['rights'] === '4') {
    $query = str_replace('#AND#', " and b.Responsible = '" . $_SESSION['id'] . "'", $sql);
    $query = str_replace('#SELECT#', "", $query);
    $result = $conn->Query($query);
} else if($_SESSION['rights'] === '5') {
    $query = str_replace('#AND#', " and b.Worker = '" . $_SESSION['id'] . "'", $sql);
    $query = str_replace('#SELECT#', "", $query);
    $result = $conn->Query($query);
}
if ($result) {
    if ($_SESSION['rights'] !== '1') {
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
    }
} else $table = 'Немає доступних записів для вас.';
$table2 = '';
if ($_SESSION['rights'] === '4') {
    $table2 .= '<table id="additionalTask">';
    $table2 .= '<td><input class="shown" placeholder="Локація"></td>
                    <td><input class="shown" placeholder="Початок"></td>
                    <td><input class="shown" placeholder="Відповідальний"></td>';
    $table2 .= '</table><div class="save" id="saveTask" >Зберегти</div>';
}