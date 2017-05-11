<?php
$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"] = "Erreur";
$aResponse["message"]["type"] = "error";
$aResponse["message"]["text"] = "Référence promesse inconnue !";
$aResponse["durationMessage"] = "2000";
$aResponse["durationRedirect"] = "2";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();
$aResponse["redirect"] = "";


if(isset($_POST["reference"])){
    $plegdeList= new PledgeListe();
    $plegdeList->applyRules4Reference($_POST["reference"]);
    $plegdes=$plegdeList->getPage();
    if(count($plegdes)==1){
        $group_id= $plegdes[0]["group_id"];
        $aResponse["message"]["title"] = "";
        $aResponse["message"]["type"] = "success";
        $aResponse["message"]["text"] = "Référence promesse identifiée !";
        $aResponse["durationMessage"] = "0";
        $aResponse["durationRedirect"] = "0";
        SessionService::set("last-group-id",$group_id);
        SessionService::set("last-pledge-ref",$_POST["reference"]);
        $aResponse["redirect"] = "/dons/ma-participation.html?display=true";
    }



}

//return
$aDataScript['data'] = json_encode($aResponse);