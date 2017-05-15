<?php
$transaction=new Transaction();
if(isset($_GET["id"])){
    $transaction=new Transaction(array("id"=>intval($_GET["id"])));
    $transaction->hydrateFromBDD(array("*"));
    if($transaction->getGroup_id()!=$oMe->getGroup_id()){
        $transaction=new Transaction();
    }
}

if(isset($_GET["from_pledge"])){

    $pledge=new Pledge(array("id"=>intval($_GET["from_pledge"])));
    $pledge->hydrateFromBDD(array("*"));

    if( $pledge->getGroup_id()==$oMe->getGroup_id()){
        $transaction=new Transaction();
        $transaction->setAmount($pledge->getAmount());
        $transaction->setPledge_id($pledge->getId());
        $transaction->setReference($pledge->getReference());

    }
}


$aDataScript["transaction"]= $transaction;