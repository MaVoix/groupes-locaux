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
$aMandatoryFields = array("civility","name", "firstname", "email","tel","ad1","city","zipcode","amount");

$aEngagements=array();
//verification des engagements 1 à 2
// Pas d'engagements pour les promesses de don
// for($i=1;$i<=2;$i++){
//     $aEngagements[]="engagement-a".$i;
// }
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

$group = new Group();
if($nError==0) {
    //verification de l'existence du groupe
    $group = new Group(array("id" => intval($_POST["group_id"])));
    $group->hydrateFromBDD(array('*'));
    $subkey=sha1(substr($group->getKey_edit(),0,10).$group->getId());
    if(!isset($_POST["group_subkey"]) || $subkey!=$_POST["group_subkey"] || intval($group->getId())==0){
        $aResponse["message"]["text"] = "Impossible d'enregistrer votre promesse de don sur ce collectif local !";
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
    $pledge->setAd1($_POST["ad1"]);
    $pledge->setAd2($_POST["ad2"]);
    if(isset($_POST["ad3"])){
        $pledge->setAd3($_POST["ad3"]);
    }
    $pledge->setZipcode($_POST["zipcode"]);
    $pledge->setCity($_POST["city"]);
    $pledge->setAmount($nAmount);
    $pledge->setGroup_id(intval($_POST["group_id"]));
    $pledge->save();

    //reference
    $pledge->setReference(Pledge::genereRandomReference( $pledge->getId()));
    $pledge->save();

    //envoi du mail
    $TwigEngine = App::getTwig();
    $sBodyMailHTML = $TwigEngine->render("visitor/mail/don-body.html.twig", [
        "group" => $group,
        "pledge" =>$pledge
    ]);
    $sBodyMailTXT = $TwigEngine->render("visitor/mail/don-body.txt.twig", [
        "group" => $group,
        "pledge" =>$pledge
    ]);

    Mail::sendMail( $pledge->getEmail(), "Confirmation de promesse de don", $sBodyMailHTML, $sBodyMailTXT, true);

    SessionService::set("last-pledge-save-id",$pledge->getId());

    $aResponse["durationMessage"] = "2000";
    $aResponse["durationRedirect"] = "2000";
    $aResponse["durationFade"] = "10000";
    $aResponse["message"]["title"] = "";
    $aResponse["message"]["type"] = "success";
    $aResponse["message"]["text"] = "Merci !";
    $aResponse["redirect"] = "/dons/merci.html";

}


//return
$aDataScript['data'] = json_encode($aResponse);
