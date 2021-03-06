<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"] = "Erreur";
$aResponse["message"]["type"] = "error";
$aResponse["message"]["text"] = "Tous les champs suivi de * sont obligatoires !";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();

$nError=0;
$nAmount=0;
$bEdit=false;
$transaction=new Transaction();

if(isset($_POST["id"])){
    $transaction=new Transaction(array("id"=>intval($_POST["id"])));
    $transaction->hydrateFromBDD(array("*"));
    if($transaction->getGroup_id()!=$oMe->getGroup_id()){
        $nError++;
        $aResponse["message"]["text"] = "Impossible d'enregistrer cette transaction";
    }else{
        $bEdit=true;
    }
}

//mandatory fields
$aMandatoryFields = array("reference","amount", "income_expense");


foreach ($aMandatoryFields as $sField) {
    if (!isset($_POST[$sField]) || $_POST[$sField] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => $sField));
        $_POST[$sField] = "";
    }
}

//verification du montants
if($nError==0){
    $nAmount= floatval(str_replace(array(" ",",","-"),array("",".",""),$_POST["amount"]));
    if( $nAmount<=0){
        $aResponse["message"]["text"] = "Le montant saisi n'est correct (saisir un nombre positif)";
        array_push($aResponse["required"], array("field" => "amount"));
        $nError++;
    }
    if($_POST["income_expense"]=="expense"){
        $nAmount=-1*$nAmount;
    }
}

//verification de la référence promesse :
$pledge_id=null;
if($nError==0 && isset($_POST["is_pledge"]) && $_POST["is_pledge"]=="ok"){
    $plegdeList= new PledgeListe();
    $plegdeList->applyRules4Reference($_POST["reference"],$oMe->getGroup_id());
    $plegdes=$plegdeList->getPage();
    if(count($plegdes)!=1){
        $aResponse["message"]["text"] = "Aucune promesse correspondant à la référence saisie.";
        array_push($aResponse["required"], array("field" => "reference"));
        $nError++;
    }else{
        $pledge_id= $plegdes[0]["id"];

    }
}


if($nError==0){
    if(!$bEdit){
        $transaction=new Transaction();
        $transaction->setDate_created(date("Y-m-d H:i:s"));
        $transaction->setUser_id($oMe->getId());
    }else{
        $transaction->setDate_amended(date("Y-m-d H:i:s"));
    }
    $transaction->setReference($_POST["reference"]);
    $transaction->setGroup_id($oMe->getGroup_id());
    $transaction->setAmount($nAmount);
    $transaction->setPledge_id($pledge_id);
    if($nAmount>0){
        $transaction->setIncome(abs($nAmount));
    }else{
        $transaction->setExpense(abs($nAmount));
    }
    if(isset($_POST["comment"]) && $_POST["comment"]!=""){
        $transaction->setComment(trim($_POST["comment"]));
    }
    $transaction->save();

    if(!is_null($pledge_id)){
        $pledge=new Pledge(array("id"=>$pledge_id));
        $pledge->setDate_completed(date("Y-m-d H:i:s"));
        $pledge->save();
    }

    $aResponse["durationMessage"] = "2000";
    $aResponse["durationRedirect"] = "2000";
    $aResponse["durationFade"] = "10000";
    $aResponse["message"]["title"] = "";
    $aResponse["message"]["type"] = "success";
    $aResponse["message"]["text"] = "Enregistrement effectué !";
    $aResponse["redirect"] = "/transactions/suivi-transaction.html";

}


//return
$aDataScript['data'] = json_encode($aResponse);