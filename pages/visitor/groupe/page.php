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
    $aDataScript["group"]=$Group;
    $aDataScript["postersCost"] = round($Group->getPosters() * 1.5554285714, 2);
    $aDataScript["professions_de_foiCost"] = round($Group->getProfessions_de_foi() * 0.0106656343, 2);
    $aDataScript["ballotsEtrCost"] = round($Group->getBallots() * 0.0028419347, 2);
    $aDataScript["ballotsCost"] = round($Group->getBallots() * 0.002532821, 2);
    $aDataScript["smallExpensesCost"] = 100;
    $aDataScript["accountantCost"] = 150;
    $aDataScript["bankingCost"] = 150;
    $aDataScript["accountantEtrCost"] = 115;
    $aDataScript["postalCost"] = 60;
    $aDataScript["remainingCost"] =
    $Group->getAmount_target()
    - $aDataScript["postalCost"]
    - $aDataScript["accountantCost"]
    - $aDataScript["smallExpensesCost"]
    - $aDataScript["postersCost"]
    - $aDataScript["bankingCost"]
    - $aDataScript["professions_de_foiCost"]
    - $aDataScript["ballotsCost"];
    $aDataScript["remainingEtrCost"] =
    $Group->getAmount_target()
    - $aDataScript["bankingCost"]
    - $aDataScript["postalCost"]
    - $aDataScript["accountantEtrCost"]
    - $aDataScript["smallExpensesCost"]
    - $aDataScript["postersCost"]
    - $aDataScript["ballotsEtrCost"];
}
