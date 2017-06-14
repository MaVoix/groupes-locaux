<?php
$bFoundGroup=false;
$Group = new Group();
if(isset($_GET["id"])){
    $Group = new Group(array("id" =>intval($_GET["id"])));
    $Group->hydrateFromBDD(array('*'));
    if($Group->getState()=="online"){
        $bFoundGroup=true;
    }
}

if(!$bFoundGroup){
    header("HTTP/1.0 404 Not Found");
}else{
  $aDataScript["pledge_amount"]=$Group->getAmount_pledge();
  $aDataScript["income_amount"]=$Group->getAmount_income();
  $aDataScript["group"]=$Group;
}
