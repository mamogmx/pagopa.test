<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once "config.php";
$dbh = getDb();
$iuv = $_REQUEST["iuv"];
$esito = $_REQUEST["esito"];
$uid = (in_array($esito,Array('OK','ATTESA_RT')))?(randomKey(20)):('');
$tms = date('Y/m/d H:i:s');
$result = Array(
    "success" => 0,
    "uid" => $uid,
    "esito" => $esito,
    "time" => $tms,
    "message" => ""
);

$sql = "UPDATE pe.richiesta SET esito=?, data_pagamento=?, uidriscossione=? WHERE iuv = ?";
$stmt = $dbh->prepare($sql);

if(!$stmt->execute(Array($esito,$tms,$uid,$iuv))){
    $r = $stmt->errorInfo();
    $result["message"] = $r[1];    
    
}
else{
    $result["success"] = 1;
}
header('Content-Type: application/json; charset=utf-8');
print json_encode($result);
return;