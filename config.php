<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getDb($params=Array()){
    $dsn = sprintf('pgsql:dbname=%s;host=%s;port=%s',DB_NAME,DB_HOST,DB_PORT);
    $conn = new PDO($dsn, DB_USER, DB_PWD);
    return $conn;
}
function randomKey($length) {
    $pool = array_merge(range(0,9));

    for($i=0; $i < $length; $i++) {
        $key .= $pool[mt_rand(0, count($pool) - 1)];
    }
    return $key;
}

function debug($fname,$data,$mode='a+'){
    if (DEBUG_ENABLED===FALSE) return;
    $f = fopen($fname,$mode);
    ob_start();
    print_r($data);
    $text = ob_get_contents();
    ob_end_clean();
    fwrite($f,$text);
    fclose($f);
    
}
define('DB_DRIVER','pdo_pgsql');
define('DB_HOST','195.88.6.158');
define('DB_NAME','pippo');
define('DB_USER','gwAdmin');
define('DB_PWD','!{!dpQ3!Hg7kdCA9');

define('DB_PORT','5434');
define('DEBUG_ENABLED',TRUE);

$richiesta = Array(
    "codTrans" => "codtrans",
    "mac" => "mac",
    "alias" => "alias",
    "tipoPagatore" => "tipopagatore",
    "anagraficaPagatore" => "anagraficapagatore",
    "identificativoFiscalePagatore" => "identificativofiscalepagatore",
    "email" => "email",
    "urlBack" => "urlback",
    "urlPost" => "urlpost",
    "urlOk" => "urlok"
);
$pagamenti = Array(
    "importo" => "importo",
    "ibanBeneficiario" => "ibanbeneficiario",
    "divisa" => "divisa",
    "causale" => "causale",
    "provinciaResidenza" => "provinciaresidenza",
    "commissioneCaricoPA" => "commissionecaricopa"
);