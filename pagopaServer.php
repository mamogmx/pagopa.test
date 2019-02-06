<?php
require_once "config.php";

$d = $_REQUEST["richiesta"];
if (!$d){
    $result["error"] = Array("NO DATA");
    header('Content-Type: application/json; charset=utf-8');
    print json_encode($result);
    die();
};
$data = json_decode($d,TRUE);
if ($avvisoPagamento==1){
	$data["metodo"]='BOLLETTINO';
	$iuv = randomKey(16);
	$importo = $data["listaDatiSingoloPagamento"][0]["importo"];
}
else{
	$data["metodo"]='ONLINE';
	$iuv = randomKey(32);
	$importo = $data["importo"];
}
debug('REQUEST.txt',$data);
//$iuv = randomKey(32);
$keys = Array("iuv");
$values = Array($iuv);
$fValues = Array("?");
$sqlRichiesta = "INSERT INTO pagopa.richiesta(codtrans, metodo, iuv,nominativo, cf, importo, data_store, data_ricezione) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?);";
$dbh = getDb();
$stmt = $dbh->prepare($sqlRichiesta);
$dati = Array($data["codTrans"],$data["metodo"],$iuv,$data["anagraficaPagatore"],$data["identificativoFiscalePagatore"],$importo/100,$d,date('Y/m/d H:i:s'));
if($stmt->execute($dati)){
	debug("Success.txt",$dati);
	$result = Array("iuv"=>$iuv);
	header('Content-Type: application/json; charset=utf-8');
	print json_encode($result);
}
else{
	debug("Error.txt",$stmt->errorInfo());
	debug("Error.txt",$dati);
}
/*
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
*/
?>