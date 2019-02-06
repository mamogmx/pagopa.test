<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "config.php";
$iuv = $_REQUEST["iuv"];

//$dbh = getDb();
//$sql = "SELECT * FROM richiesta WHERE iuv = ?";
//$stmt = $dbh->prepare($sql);

//if(!$stmt->execute(Array($iuv))){
//    die(">p>Errore </p>");
//}
//$res = $stmt->fetch(PDO::FETCH_ASSOC);

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
        <form id="richiesta" method="POST" action="http://195.168.2.8:10080/pagopa/SUAP-ClaSpezia-M1/PO0XRTSPD693/ricevutaTransazione">
            <input type="hidden" name="alias" value="SUAP-ClaSpezia-M1"/>
            <input type="hidden" name="IUV" value="0019000000000000000000000475778"/>
            <input type="hidden" name="codTrans" value="8CCF206TKCX9BMPXEEA4ABDAPVFB6P"/>
            <div class="container-fluid">
                <div class="form-group">
                    <label for="nome">Nominativo:</label>
                    <input type="email" class="form-control" id="email" value="marco.carbone.shop@gmail.com" disabled>
                </div>        
                <div class="form-group">
                    <label for="email">Indirizzo Email:</label>
                    <input type="email" class="form-control" id="nome" value="marco.carbone.shop@gmail.com" disabled>
                </div>
                <div class="form-group">
                    <label for="importo">Importo:</label>
                    <input type="input" class="form-control" id="importo" value="10" disabled>
                </div>
            
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
