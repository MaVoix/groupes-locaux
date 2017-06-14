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
  $aDataScript["postersCost"] = round($Group->getPosters() * 1.8161, 2);
  $aDataScript["professions_de_foiCost"] = round($Group->getProfessions_de_foi() * 0.011613687, 2);
  $aDataScript["ballotsEtrCost"] = round($Group->getBallots() * 0.0028419347, 2);
  $aDataScript["ballotsCost"] = round($Group->getBallots() * 0.003056503, 2);
  $aDataScript["accountantCost"] = 150;
  $aDataScript["postExpenses"] = $Group->getPost_expenses();
  $aDataScript["emailingExpenses"] = $Group->getEmailing_expenses();
  $aDataScript["smallExpenses"] = $Group->getSmall_expenses();
  $aDataScript["bankingFees"] = $Group->getBanking_fees();
}
