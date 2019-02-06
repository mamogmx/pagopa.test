<?php
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


$data = $_REQUEST;
$data["time"] = date("d-m-Y H:i:s");
$fname = generateRandomString();
ob_start();
print_r($data);
$res = ob_get_contents();
ob_end_flush();
$f = fopen("response/$fname.txt",'w');
fwrite($f,$res);
fclose($f);
?>
