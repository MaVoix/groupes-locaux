<?php


//liste des transaction
 $TransactionList= new TransactionListe();
$TransactionList->applyRules4Group($oMe->getGroup_id());
$data=$TransactionList->getPage();
$aDataScript["transactions"]=array();
foreach($data as $row){
    $transaction=new Transaction(array("id"=>$row["id"]));
    $transaction->hydrate($row);
    $aDataScript["transactions"][]= $transaction;
}


