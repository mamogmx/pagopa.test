<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ERROR);
require_once "config.php";

$d = $_REQUEST["richiesta"];
if (!$d){
    $result["error"] = Array("NO DATA");
    header('Content-Type: application/json; charset=utf-8');
    print json_encode($result);
    die();
};
$data = json_decode($d,TRUE);
debug('REQUEST.txt',$data);
$iuv = randomKey(32);
$keys = Array("iuv");
$values = Array($iuv);
$fValues = Array("?");
$sqlRichiesta = "INSERT INTO richiesta(%s) VALUES(%s);";
$sqlPagamento = "INSERT INTO pagamenti(%s) VALUES(%s);";
$j = 0;
foreach($data as $k=>$v){
    if (in_array($k,array_keys($richiesta))){
        $keys[] = $richiesta[$k];
        $values[] = $v;
        $fValues[] = "?";
    }
    
    if ($k == "listaDatiSingoloPagamento" && is_array($v)){
        debug('PAGAMENTI',$v);
        for($j=0;$j<count($v);$j++){
            $keysP[$j] = Array("iuv");
            $valuesP[$j] = Array($iuv);
            $fValuesP[$j] = Array("?");
            
            foreach($v[$j] as $kk=>$vv){
                $keysP[$j][] = $pagamenti[$kk];
                $valuesP[$j][] = $vv;
                $fValuesP[$j][] = "?";
            }
        }
        
    }
}
debug('FIELDS.text',$keysP);
debug('FIELDS.text',$valuesP);
$result = Array("iuv" => $iuv);
$dbh = getDb();
$fields = implode(",",$keys);
$sql = sprintf($sqlRichiesta,$fields,implode(",",$fValues));
debug('SQL.text', $sql);
$stmt = $dbh->prepare($sql);
if (count($values)){
    if(!$stmt->execute($values)){
        $result["error"][] = "Errore nella Query $sqlRichiesta";
        $result["error"][] = $stmt->errorInfo();
        unset($result["iuv"]);
    }
    else{

        foreach($keysP as $k => $v){
            $sql = sprintf($sqlPagamento,implode(",",$v),implode(",",$fValuesP[$k]));
            debug('SQL.text', $sql);
            $stmt = $dbh->prepare($sql);
            debug('VALUES.text', $valuesP[$k]);
            if(!$stmt->execute($valuesP[$k])){
                $result["error"][] = $stmt->errorInfo();
            }
        }
    }
}
else{
    $result["error"] = Array("NO DATA");
}

header('Content-Type: application/json; charset=utf-8');
print json_encode($result);
