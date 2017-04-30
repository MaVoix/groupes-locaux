<?php

$nIdSaved=SessionService::get("last-pledge-save-id");
if(isset($nIdSaved)){
    $pledge=new Pledge(array("id"=>SessionService::get("last-pledge-save-id")));
    $pledge->hydrateFromBDD(array("*"));
    $aDataScript["pledge"]=$pledge;
    $aDataScript["group"]=$pledge->group();
}
