<?php
    $connection = @mysqli_connect('localhost', 'root', '123Adm1', $_GET['db']);
 if (mysqli_connect_error())
    $response['status'] = 'error';

if ($connection) {
    $filename = '../../backup.sql';
    $handle = fopen($filename, "r+");
    $contents = fread($handle, filesize($filename));

    $sql = explode(';', $contents);
    foreach ($sql as $query) {
        $result = mysqli_query($connection, $query);
    }
    fclose($handle);
    $response['status'] = 'success';
} else $response['status'] = 'error';
echo json_encode($response);