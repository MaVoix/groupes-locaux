<?php
$transaction=new Transaction();
if(isset($_GET["id"])){
    $transaction=new Transaction(array("id"=>intval($_GET["id"])));
    $transaction->hydrateFromBDD(array("*"));
    if($transaction->getGroup_id()!=$oMe->getGroup_id()){
        $transaction=new Transaction();
    }
}
$aDataScript["transaction"]= $transaction;