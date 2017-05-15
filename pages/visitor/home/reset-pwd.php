<?php

$nError=0;
if( isset($_GET["k"]) ){
    $liste=new UserListe();
    $liste->applyRules4Key($_GET["k"]);
    $aUser=$liste->getPage();
    if(count($aUser)==1){
        $user=new User(array("id"=>$aUser[0]["id"]));
        $user->hydrate($aUser[0]);
        $aDataScript["user"]=$user;
        SessionService::set("last-user-pass-id", $user->getId());
    }else{
        $nError++;
    }
}else{
    $nError++;
}
$aDataScript["error"]=$nError;