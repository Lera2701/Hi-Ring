<?php

$bytes = openssl_random_pseudo_bytes(40);
$salt = bin2hex(openssl_random_pseudo_bytes(5));
$hex = bin2hex($bytes);
$key = $hex;
if (strpos($_SERVER['REQUEST_URI'], 'ua') === false) {
    $file = fopen('../1281.txt', 'a+');
    $file1 = fopen('../1282.txt', 'a+');
} else {
    $file = fopen('../../1281.txt', 'a+');
    $file1 = fopen('../../1282.txt', 'a+');
}

$cipher = "aes-128-gcm";
$ivlen = openssl_cipher_iv_length($cipher);
$iv = openssl_random_pseudo_bytes($ivlen);

if (strlen(file_get_contents(!str_contains($_SERVER['REQUEST_URI'], 'ua') ? '../1281.txt' : '../../1281.txt')) < 1) {
    fwrite($file, $salt.$key.bin2hex($iv));
}
if (strlen(file_get_contents(!str_contains($_SERVER['REQUEST_URI'], 'ua') ? '../1282.txt' : '../../1282.txt')) < 1) {
    fwrite($file1, strlen(bin2hex($iv)) . ' ' . strlen($salt));
}
fclose($file);
fclose($file1);

function Encrypt($data) {
    $plaintext = $data;
    $cipher = "aes-128-gcm";
    $file1 = file_get_contents(!str_contains($_SERVER['REQUEST_URI'], 'ua') ? '../1282.txt' : '../../1282.txt');
    $iv_len = substr($file1, 0, strpos($file1, ' '));
    $salt_len = substr($file1, strpos($file1, ' ')+1);
    $file = file_get_contents(!str_contains($_SERVER['REQUEST_URI'], 'ua') ? '../1281.txt' : '../../1281.txt');
    $iv = substr($file, -$iv_len);
    $key = substr($file, $salt_len, strlen($file) - $salt_len - $iv_len);
    if (in_array($cipher, openssl_get_cipher_methods()))
    {
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv, $tag);
        $file2 = fopen(!str_contains($_SERVER['REQUEST_URI'], 'ua') ? '../1283.txt' : '../../1283.txt', 'a+');
        if (!str_contains(file_get_contents((!str_contains($_SERVER['REQUEST_URI'], 'ua') ? '../1283.txt' : '../../1283.txt')), $ciphertext)) {
            fwrite($file2, $ciphertext . '*' . bin2hex($tag) . '*');
        }
        fclose($file2);
    }
    return $ciphertext;
}

function Decrypt($text) {
    $cipher = "aes-128-gcm";
    $file1 = file_get_contents(!str_contains($_SERVER['REQUEST_URI'], 'ua') ? '../1282.txt' : '../../1282.txt');
    $iv_len = substr($file1, 0, strpos($file1, ' '));
    $salt_len = substr($file1, strpos($file1, ' ')+1);
    $file = file_get_contents(!str_contains($_SERVER['REQUEST_URI'], 'ua') ? '../1281.txt' : '../../1281.txt');
    $iv = substr($file, -$iv_len);
    $key = substr($file, $salt_len, strlen($file) - $salt_len - $iv_len);
    $file3 = file_get_contents(!str_contains($_SERVER['REQUEST_URI'], 'ua') ? '../1283.txt' : '../../1283.txt');
    $file3 = explode('*', $file3);
    $tag = $file3[array_search($text, $file3) + 1];
    $original_plaintext = openssl_decrypt($text, $cipher, $key, $options=0, $iv, hex2bin($tag));
    return $original_plaintext;
}
/*$pass = [];
$pass[] = '111';
$pass[] = '123123';
$pass[] = '321321';
$pass[] = '333333';
$pass[] = '444444';
$pass[] = '666666';
$pass[] = '000000';
foreach ($pass as $p) {
    $data = Encrypt($p);
    echo $data.' - ';
    echo Decrypt($data).'<br>';
}*/




