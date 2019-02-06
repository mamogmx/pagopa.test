<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "config.php";

$keys = array_keys($_REQUEST);
$dbh = getDb();
//print_r($_REQUEST);
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
    $iuv = base64_decode($_REQUEST["iuv"]);
    $iuv = ($iuv)?($iuv):($_REQUEST["iuv"]);
    $sql = "SELECT * FROM pagopa.richiesta WHERE iuv=?;";
    $stmt = $dbh->prepare($sql);

    if(!$stmt->execute()){
        print_r($stmt->errorInfo());
	die();
    }
}
else{
    $sql = "SELECT * FROM pagopa.richiesta WHERE data_pagamento IS NULL;";
    $stmt = $dbh->prepare($sql);

    if(!$stmt->execute()){
        print_r($stmt->errorInfo());
	die();
    }
}
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

for($i=0;$i<count($res);$i++){
	
	$text = sprintf("<option value='%s'>%s - %s -IMPORTO : %s Euro con IUV : %s</option>",$res[$i]["id"],$res[$i]["metodo"],$res[$i]["nominativo"],$res[$i]["importo"],$res[$i]["iuv"]);
	$arrOptions[] = $text;
}
if (count($res)==0) $arrOptions[]="<option value=''>Nessuna richiesta da processare</option>";
$options=implode("\n",$arrOptions);

$params = Array(
    "alias"=>"SUAP-Claspezia-M1",
    "codTrans"=>"null",
    "importo"=>"1600",
    "mac"=>"3f24c3df5c0a5f997c3527817957f15c2492c305",
    "brand"=>"",
    "tContab"=>"I",
    "esito"=>"ATTESA_RT",
    "divisa"=>"EUR",
    "data"=>"",
    "orario"=>"",
    "email"=>"athei@libero.it",
    "cognome"=>"REAL+SERVICE+S.R.L.",
    "nome"=>"REAL+SERVICE+S.R.L.",
    "IUV"=>"0019000000000000000000000005122",
    "uidriscossione"=>"",
    "ParametriAggiuntivi"=>"null"
);

$formTemplate =<<<EOT
        <form id="richiesta" method="POST">
            <input type="hidden" name="alias" value="SUAP-ClaSpezia-M1"/>
            <input type="hidden" name="IUV" value="0019000000000000000000000475778"/>
            <input type="hidden" name="codTrans" value="8CCF206TKCX9BMPXEEA4ABDAPVFB6P"/>
            <div class="container-fluid" style="padding:50px;">
                <div class="form-group">
                    <label for="nome">Seleziona la richiesta da Processare</label>
                    <select name="id-richiesta" id="richiesta">
						$options
					</select>
                </div>        
                
            <hr>
                <div class="row-fluid">
                    <div class="span12">
                    <button type="submit" class="btn btn-primary" name="esito" value="OK" ">Esito OK</button>
                    <button type="submit" class="btn btn-primary" name="esito" value="ATTESA_RP" >Attesa RP</button>
                    <button type="submit" class="btn btn-primary" name="esito" value="ANNULLO" >Annulla Transazione</button>
                    <button type="submit" class="btn btn-primary" name="esito" value="KO" >Errore Transazione</button>
                   <!-- <button type="submit" class="btn btn-primary" name="esito" value="IN_ATTESA_PSP" formaction=""></button>-->
                    </div>
                </div>
                
            </div>
        </form>    
EOT;
$form = $formTemplate;
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
