<?php
$connection = @mysqli_connect('localhost','root','123Adm1',$_GET['db']);
if (mysqli_connect_error())
    $message = 'Error, please, try again';
else $message = 'Successfully imported';

if ($connection) {
    $filename = '../backup.sql';
    $handle = fopen($filename, "r+");
    $contents = fread($handle, filesize($filename));

    $sql = explode(';', $contents);
    foreach ($sql as $query) {
        $result = mysqli_query($connection, $query);
    }
    fclose($handle);
}
echo json_encode($message);