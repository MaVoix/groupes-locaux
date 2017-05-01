<?php

$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"] = "Erreur";
$aResponse["message"]["type"] = "error";
$aResponse["message"]["text"] = "Impossible de valider cette promesse";
$aResponse["durationMessage"] = "2000";
$aResponse["durationRedirect"] = "2";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();
$aResponse["redirect"] = "/dons/suivi-promesse.html";


if(isset($_POST["id"])){
    $pledge=new Pledge(array("id"=>intval($_POST["id"])));
    $pledge->hydrateFromBDD(array("*"));
    if($pledge->getGroup_id()==$oMe->getGroup_id()){
        $pledge->setDate_deleted("Y-m-d H:i:s");
        $pledge->save();
        $aResponse["message"]["title"] = "";
        $aResponse["message"]["type"] = "success";
        $aResponse["message"]["text"] = "Promesse supprimée !";
        $aResponse["redirect"] = "/dons/suivi-promesse.html";
    }
}

//return
$aDataScript['data'] = json_encode($aResponse);
