<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "config.php";

$keys = array_keys($_REQUEST);
$dbh = getDb();

if(in_array('esito',$keys) && in_array('id-richiesta',$keys)){
	$id = $_REQUEST["id-richiesta"];
	$esito = $_REQUEST["esito"];
	
	$sql = "SELECT * FROM pagopa.richiesta WHERE id = ?";
	$stmt = $dbh->prepare($sql);

	if(!$stmt->execute(Array($id))){
		print_r($stmt->errorInfo());
		die();
	}
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
        $iuv = $res["iuv"];
        $importo = $res["importo"];
	$data = json_decode($res["data_store"],TRUE);
	$url = $data["urlPost"];
	echo "<pre>";print_r($data);echo "</pre>";
	
	//Store Information about Payment
	$sql = "UPDATE pagopa.richiesta  SET data_pagamento=?, uidriscossione=? WHERE id=?;";
	$stmt = $dbh->prepare($sql);
	$uid = randomKey(20);
	if(!$stmt->execute(Array(date('Y/m/d H:i:s'),$uid,$id))){
		print_r($stmt->errorInfo());
		die();
	}
	//The data you want to send via POST
	
	
	$fields = Array(
		"alias"=>$data["alias"],
		"codTrans"=>$data["codTrans"],
		"importo"=> $importo,
		"mac"=>$data["mac"],
		"brand"=>"",
		"tContab"=>"I",
		"esito"=>$esito,
		"divisa"=>"EUR",
		"data"=>"",
		"orario"=>"",
		"email"=>$data["email"],
		"cognome"=>$data["nominativo"],
		"nome"=>$data["nominativo"],
		"IUV"=>$iuv,
		"uidriscossione"=>$uid,
		"ParametriAggiuntivi"=>"",
		"time" => date('Y/m/d H:i:s')
	);

	//url-ify the data for the POST
	$fields_string = http_build_query($fields);

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//So that curl_exec returns the contents of the cURL; rather than echoing it
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        debug('RESPONSE.txt',$fields);
        debug('RESPONSE.txt',$fields_string);
	//execute post
	$result = curl_exec($ch);
	echo $result;
}

if (in_array('iuv',$keys)){
    $mode = (empty(htmlspecialchars(base64_decode($iuv, true))))?('text'):('base64');
    $template = ($mode=='base64')?("template_2"):("template_1");
    
    //$iuv = ($mode=='base64')?(base64_decode($_REQUEST["iuv"])):($_REQUEST["iuv"]);
    $iuvDecoded = base64_decode($_REQUEST["iuv"]);
    $iuv = $_REQUEST["iuv"];
    $sql = "SELECT * FROM pagopa.richiesta WHERE iuv in (?,?);";
    $stmt = $dbh->prepare($sql);

    if(!$stmt->execute(Array($iuv,$iuvDecoded))){
    print_r($stmt->errorInfo());
	print_r(Array($iuv,$iuvDecoded));
	die($sql);
    }
    $rr = $stmt->fetch(PDO::FETCH_ASSOC);
    $iuv = $rr["iuv"];
    $metodo = $rr["metodo"];
    $template = ($metodo=='ONLINE')?("template_2"):("template_1");
    $sql = "SELECT * FROM pagopa.richiesta WHERE iuv = ?;";
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute(Array($iuv))){
        print_r($stmt->errorInfo());
        die("B");
    }

}
else{

    $template = "template_2";
    $sql = "SELECT * FROM pagopa.richiesta WHERE data_pagamento IS NULL;";
    $stmt = $dbh->prepare($sql);

    if(!$stmt->execute()){
        print_r($stmt->errorInfo());
	die("A");
    }
}

$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

for($i=0;$i<count($res);$i++){	
    $text = sprintf("<option value='%s'>%s - %s -IMPORTO : %s Euro con IUV : %s</option>",$res[$i]["id"],$res[$i]["metodo"],$res[$i]["nominativo"],$res[$i]["importo"],$res[$i]["iuv"]);
    $arrOptions[] = $text;
}
if (count($res)==0) $arrOptions[]="<option value=''>Nessuna richiesta da processare</option>";
$options=implode("\n",$arrOptions);

$f=fopen("./template/$template.html",'r');
$formTemplate = fread($f,filesize("./template/$template.html"));
switch($template){
    case "template_1":
        $res = $res[0];
        print_r($res);
        $importo = $res["importo"];
	$data = json_decode($res["data_store"],TRUE);
	$url = $data["urlPost"];
        $fields = Array(
                "url"=>$url,
                "iuv"=>$iuv,
		"alias"=>$data["alias"],
		"codTrans"=>$data["codTrans"],
		"importo"=> $importo,
		"mac"=>$data["mac"],
		"brand"=>"",
		"tContab"=>"I",
		"esito"=>$esito,
		"divisa"=>"EUR",
		"data"=>"",
		"orario"=>"",
		"email"=>$data["email"],
		"cognome"=>$res["nominativo"],
		"nome"=>$res["nominativo"],
		"ParametriAggiuntivi"=>"",
		"time" => date('Y/m/d H:i:s')
	);
        foreach($fields as $k=>$v){
            $formTemplate = str_replace("[$k]",$v, $formTemplate);
        }
        $form = $formTemplate;
        break;
    case "template_2":
        $form = sprintf($formTemplate,$options);
        break;
    default:
        $form = "";
}

?>
<html>
    <head>
        <link rel="stylesheet" href="./css/bootstrap.min.css">
        <script src="./js/jquery-3.2.1.js"></script>
        <script src="./js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php
        print $form;
        ?>
    </body>
</html>
