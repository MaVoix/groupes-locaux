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

//mandatory fields
$aMandatoryFields = array("civility","name", "firstname", "email","tel","zipcode","amount");

$aEngagements=array();
//verification des engagements 1 à 2
for($i=1;$i<=2;$i++){
    $aEngagements[]="engagement-a".$i;
}
$aMandatoryFields=array_merge($aMandatoryFields,$aEngagements);

foreach ($aMandatoryFields as $sField) {
    if (!isset($_POST[$sField]) || $_POST[$sField] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => $sField));
        $_POST[$sField] = "";
    }
}

if($nError==0){
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $aResponse["message"]["text"] = "L'adresse e-mail saisie n'est pas correcte";
        array_push($aResponse["required"], array("field" => "email"));
        $nError++;
    }
}

if($nError==0){
    $nAmount= floatval(str_replace(array(" ",","),array("","."),$_POST["amount"]));
    if( $nAmount<=0){
        $aResponse["message"]["text"] = "Le montant saisi n'est correct";
        array_push($aResponse["required"], array("field" => "amount"));
        $nError++;
    }
}

if($nError==0){
    $pledge=new Pledge();
    $pledge->setDate_created(date("Y-m-d H:i:s"));
    $pledge->setCivility($_POST["civility"]);
    $pledge->setName($_POST["name"]);
    $pledge->setFirstname($_POST["firstname"]);
    $pledge->setEmail($_POST["email"]);
    $pledge->setTel($_POST["tel"]);
    $pledge->setZipcode($_POST["zipcode"]);
    $pledge->setAmount($nAmount);
    $pledge->setGroup_id(intval($_POST["group_id"]));
    $pledge->save();
    //TODO : finir sauvgarde avec lien vers le groupe, (verif du goupe avec la clé), génération de la réféfence et envoi du mail + redirect vers la page de remerciement

    $aResponse["durationMessage"] = "2000";
    $aResponse["durationRedirect"] = "2000";
    $aResponse["durationFade"] = "10000";
    $aResponse["message"]["title"] = "";
    $aResponse["message"]["type"] = "success";
    $aResponse["message"]["text"] = "Merci !";
    $aResponse["redirect"] = "/dons/participation.html";

}


//return
$aDataScript['data'] = json_encode($aResponse);

