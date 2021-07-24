<?php
require_once '../database.php';
require_once '../cipher.php';
if(isset($_POST['user']) && isset($_POST['pass'])) {
    $conn = new Database();
    $result = $conn->Select('employee', [0 => 'Password', 1 => 'ID', 2 => 'Rights'], 1, "`Login` = '" . $_POST['user'] . "'");
    if ($result) {
        if (Decrypt($result[0]) === $_POST['pass']) {
            $response['status'] = 'authorized';
            $_SESSION['rights'] = $result[2];
            $_SESSION['id'] = $result[1];
        }
        else $response['status'] = 'wrong password';
    } else $response['status'] = 'no such login';
    echo json_encode($response);
}

if(isset($_POST['nameCall']) && (isset($_POST['phoneCall']) || isset($_POST['emailCall']))) {
    $conn = new Database();
    $contacts = (isset($_POST['phoneCall']) ? $_POST['phoneCall'].(isset($_POST['emailCall']) ? ', '.$_POST['emailCall'] : '') : $_POST['emailCall']);
    $admin = $conn->Select('employee', 'ID', 1, '`Rights`=1');
    $result = $conn->Insert('notifications', [0 => $admin[0], 1 => $_SESSION['id'], 2 => 'Please, confirm my account. My name is '.$_POST['nameCall'].', contacts: '.$contacts, 3 => date('Y-m-d H:i:s'), 4 => 'New user'], [1 => 'MessageTo', 2 => 'MessageFrom', 3 => 'Text', 4 => 'Date', 5 => 'Tag']);
    if ($result) {
        $response['status'] = 'sent';
    } else $response['status'] = 'sent';
    echo json_encode($response);
}

if(isset($_POST['userReg']) && isset($_POST['passReg']) && isset($_POST['passReg1'])) {
    $conn = new Database();
    if ($_POST['passReg'] === $_POST['passReg1']) {
        $creds = [];
        $creds[] = $_POST['userReg'];
        $creds[] = Encrypt($_POST['passReg']);
        $creds[] = '0';
        $result = $conn->Insert('employee', $creds, [0 => 'Login', 1 => 'Password', 2 => 'Rights']);
        if ($result) {
            $response['status'] = 'registered';
            $id = $conn->Select('employee', 'ID', 1, '`ID` >= ALL(SELECT `ID` FROM employee)');
            if ($id)
                $_SESSION['id'] = $id[0];
            $_SESSION['rights'] = 0;
        } else
            $response['status'] = 'insert error';
    } else {
        $response['status'] = 'different passwords';
    }
    echo json_encode($response);
}

if(isset($_POST['action']) && $_POST['action'] === 'getNotes') {
    $conn = new Database();
    $status = 'false';
    if ($_SESSION['rights'] === '4') {
        $result = $conn->Select('employee', 'Name', 1, '`ID`='.$_SESSION['id']);
        if ($result)
            $result = $conn->Select('work', [0 => 'Location', 1 => 'StartTime', 2 => 'EndTime', 3 => 'ID'], null, '`StartTime` < CURRENT_TIME and `EndTime` > CURRENT_TIME and `ID` in (select `Work` from work_details where `Responsible`='.$result[0].')');
        if ($result) {
            $status = 'true';
            foreach ($result[0] as $row) {
                $loc = $conn->Select('location', 'Name', 1, '`ID`=' . $row[0]);
                $response['start'][] = $row[1];
                $response['end'][] = $row[2];
                $response['location'][] = $loc[0];
                $response['work_id'][] = $row[3];
            }
        }
    }
    $result = $conn->Select('notifications', [0 => 'Tag', 1 => 'Date', 2 => 'Text', 3 => 'MessageFrom'], null, '`MessageTo`=' . $_SESSION['id'], 'Date', 'DESC');
    if ($result) {
        $status = 'true';
        foreach ($result[0] as $row) {
            $response['tag'][] = $row[0];
            $response['date'][] = $row[1];
            $response['text'][] = $row[2];
            if ($_SESSION['rights'] === '1')
                $response['subject'][] = $row[3];
        }
    }
    $response['status'] = $status;
    echo json_encode($response);
}

if(isset($_POST['action']) && isset($_POST['target'])) {
    $conn = new Database();
    if ($_POST['action'] === 'delete' && $_POST['target'] === 'employee') {
        $result = $conn->Delete($_POST['target'], '`ID` IN (' . $_POST['ids'] . ')');
        if ($result)
            $response['status'] = 'deleted';
        else $response['status'] = 'deletion error';
        echo json_encode($response);
    }

    if ($_POST['action'] === 'delete' && $_POST['target'] === 'work') {
        $success = true;
        $result = $conn->Delete($_POST['target'], '`ID` IN (' . $_POST['ids'] . ')');
        if (!$result)
            $success = false;
        $result = $conn->Delete('work_details', '`ID` IN (' . $_POST['details'] . ')');
        if (!$result)
            $success = false;
        if ($success)
            $response['status'] = 'deleted';
        else $response['status'] = 'deletion error';
        echo json_encode($response);
    }

    if ($_POST['action'] === 'update' && isset($_POST['values']) && $_POST['target'] === 'employee') {
        $result = $conn->Select($_POST['target'], [0 => 'Password'], 1, '`ID`=' . $_POST['ids']);
        $values = explode(',', $_POST['values']);
        if ($result && $result[0] !== $values[4]) {
            $values[4] = Encrypt($values[4]);
        }
        $values = implode(',',$values);
        $result = $conn->Update($_POST['target'], $values, '`ID`=' . $_POST['ids']);
        if ($result)
            $response['status'] = 'updated';
        else $response['status'] = 'updating error';
        echo json_encode($response);
    }

    if ($_POST['action'] === 'update' && isset($_POST['values']) && $_POST['target'] === 'work') {
        $values = $_POST['values'];
        $columns = $_POST['columns'];
        $success = true;
        if (str_contains($_POST['columns'], 'Worker')) {
            $value = substr($_POST['values'], strrpos($_POST['values'], ',') + 1);
            $values = substr($_POST['values'], 0, strrpos($_POST['values'], ','));
            $columns = substr($_POST['columns'], 0, strrpos($_POST['columns'], ','));
            $result = $conn->Update('work_details', $value, '`ID`=' . $_POST['ids_detail'], [0 => 'worker']);
            if (!$result)
                $success = false;
        }
        if (str_contains($_POST['columns'], 'Responsible')) {
            $value = substr($values, strrpos($values, ',') + 1);
            $values = substr($values, 0, strrpos($values, ','));
            $columns = substr($columns, 0, strrpos($columns, ','));
            $result = $conn->Update('work_details', $value, '`ID`=' . $_POST['ids_detail'], [0 => 'responsible']);
            if (!$result)
                $success = false;
            //echo $result;
        }
        $columns = explode(',', $columns);

        $result = $conn->Update($_POST['target'], $values, '`ID`=' . $_POST['ids'], $columns);
        if (!$result)
            $success = false;

        if (!empty($_POST['newvalues']) && !empty($_POST['newcolumns'])) {
            $columns = explode(',', $_POST['newcolumns']);
            $values = explode(',', $_POST['newvalues']);
            if (count($columns) === count($values)) {
                for ($i = 0; $i < count($columns); $i++) {
                    if ($columns[$i] === 'Location') {
                        $result = $conn->Insert($columns[$i], [0 => $values[$i]], [0 => 'name']);
                        if (!$result)
                            $success = false;
                    } else {
                        $result = $conn->Insert('employee', [0 => $values[$i], 1 => ($columns[$i] === 'Responsible' ? '4' : '5')], [0 => 'name', 1 => 'rights']);
                        if (!$result)
                            $success = false;
                    }
                }
            }
        }
        if ($success)
            $response['status'] = 'updated';
        else $response['status'] = 'updating error';
        echo json_encode($response);

    }

    if ($_POST['action'] === 'add' && isset($_POST['values']) && $_POST['target'] === 'employee') {
        $values = explode(',', $_POST['values']);
        $values[4] = Encrypt($values[4]);

        $result = $conn->Insert($_POST['target'], $values);
        if ($result)
            $response['status'] = 'added';
        else $response['status'] = 'adding error';
        echo json_encode($response);
    }

    if ($_POST['action'] === 'add' && isset($_POST['values']) && $_POST['target'] === 'work') {
        $values = explode(',', $_POST['values']);
        $company = '';
        $location = '';
        $responsible = '';
        $worker = '';
        if ($values[0] !== '-') {
            $result = $conn->Select('company', 'ID', 1, '`Name`="' . $values[0] . '"');
            if ($result)
                $company = $result[0];
            else {
                $result = $conn->Insert('company', [0 => $values[0]], [0 => 'Name']);
                if ($result)
                    $company = $conn->getConection()->insert_id;
            }
        }
        if ($values[1] !== '-') {
            $where = '`Name`="' . $values[1] . '"';
            if ($company !== '')
                $where .= ' and `Company`="' . $company . '"';
            $result = $conn->Select('location', 'ID', 1, $where);
            //echo json_encode($result);
            if ($result)
                $location = $result[0];
            else {
                $value = [0 => $values[1]];
                $columns = [0 => 'Name'];
                if ($company !== '') {
                    $value[1] = $company;
                    $columns[1] = 'Company';
                }
                $result = $conn->Insert('location', $value, $columns);
                if ($result)
                    $location = $conn->getConection()->insert_id;
            }
        }

        if ($values[4] !== '-') {
            $value = [0 => $values[4], 1 => 4];
            $columns = [0 => 'Name', 1 => 'Rights'];
            if ($company !== '') {
                $value[2] = $values[0];
                $columns[2] = 'Company';
            }
            $result = $conn->Select('employee', 'ID', 1, '`Name`="' . $values[4] . '"');
            if ($result)
                $responsible = $result[0];
            else {
                $result = $conn->Insert('employee', $value, $columns);
                if ($result)
                    $responsible = $conn->getConection()->insert_id;
            }
        }

        if ($values[5] !== '-') {
            $value = [0 => $values[5], 1 => 5];
            $columns = [0 => 'Name', 1 => 'Rights'];
            if ($company !== '') {
                $value[2] = $values[0];
                $columns[2] = 'Company';
            }
            $result = $conn->Select('employee', 'ID', 1, '`Name`="' . $values[5] . '"');
            if ($result)
                $worker = $result[0];
            else {
                $result = $conn->Insert('employee', $value, $columns);
                if ($result)
                    $worker = $conn->getConection()->insert_id;
            }
        }

        $value = [];
        $columns = [];
        if ($location !== '') {
            $value[] = $location;
            $columns[] = 'Location';
        }
        if ($values[2] !== '-') {
            $value[] = $values[2];
            $columns[] = 'StartTime';
        }
        if ($values[3] !== '-') {
            $value[] = $values[3];
            $columns[] = 'EndTime';
        }
        if (!empty($value) && !empty($columns) && count($value) === count($columns)) {
            $result = $conn->Insert('work', $value, $columns);
            $work = $conn->getConection()->insert_id;
        }

        if ($result) {
            $value = [];
            $columns = [];
            if ($work !== '') {
                $value[] = $work;
                $columns[] = 'Work';
            }
            if ($worker) {
                $value[] = $worker;
                $columns[] = 'Worker';
            }
            if ($responsible) {
                $value[] = $values[4];
                $columns[] = 'Responsible';
            }
            if (!empty($value) && !empty($columns) && count($value) === count($columns))
                $result = $conn->Insert('work_details', $value, $columns);
        }

        //$result = $conn->Insert($_POST['target'], $values);
        if ($result)
            $response['status'] = 'added';
        else $response['status'] = 'adding error';
        echo json_encode($response);
    }
}
