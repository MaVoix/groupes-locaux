<?php


//liste des transaction
 $TransactionList= new TransactionListe();
$TransactionList->applyRules4Group($oMe->getGroup_id());
$aDataScript["transactions"]=$TransactionList->getPage();
