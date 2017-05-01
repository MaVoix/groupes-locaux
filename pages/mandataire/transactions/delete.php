<?php

$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"] = "Erreur";
$aResponse["message"]["type"] = "error";
$aResponse["message"]["text"] = "Impossible de supprimer cette transaction";
$aResponse["durationMessage"] = "2000";
$aResponse["durationRedirect"] = "2";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();
$aResponse["redirect"] = "/transactions/suivi-transaction.html";


if(isset($_POST["id"])){
    $transaction=new Transaction(array("id"=>intval($_POST["id"])));
    $transaction->hydrateFromBDD(array("*"));
    if($transaction->getGroup_id()==$oMe->getGroup_id()){
        $transaction->setDate_deleted("Y-m-d H:i:s");
        $transaction->save();
        $aResponse["message"]["title"] = "";
        $aResponse["message"]["type"] = "success";
        $aResponse["message"]["text"] = "Transaction supprimée !";
        $aResponse["redirect"] = "/transactions/suivi-transaction.html";
    }
}

//return
$aDataScript['data'] = json_encode($aResponse);