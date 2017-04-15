<?php

$oMsg = MessageService::getNode('system','entry','all-fields-required');

if(isset($_POST["email"]) && trim($_POST["email"])!="" && isset($_POST["pass"]) && trim($_POST["pass"])!=""){
    $oMsg = MessageService::getNode('connexion','login','success');
}

$aResponse=array();
$aResponse["type"]="message";
$aResponse["message"]=array();
$aResponse["message"]["title"]=$oMsg->title;
$aResponse["message"]["type"]=$oMsg->type;
$aResponse["message"]["text"]=$oMsg->text;
$aResponse["durationMessage"]="3000";
$aResponse["durationRedirect"]="1";
$aResponse["durationFade"]="500";
$aResponse["required"]=array();
$aDataScript['data']=json_encode($aResponse);