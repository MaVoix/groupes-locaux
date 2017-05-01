<?php

//liste des promesses
$PledgeList= new PledgeListe();
$PledgeList->applyRules4GroupCurrent($oMe->getGroup_id());
$aDataScript["pledges"]=$PledgeList->getPage();


//liste des transaction
/*
 $TransactionList= new TransactionListe();
$TransactionList->applyRules4Group($oMe->getGroup_id());
$aDataScript["transaction"]=$TransactionList->getPage();
*/