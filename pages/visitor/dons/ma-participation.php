<?php

$aDataScript["pledge_ref"]="";
if(isset($_GET["display"]) && $_GET["display"]=="true" && SessionService::get("last-group-id")>=0 ){
    $group=new Group(array("id"=>SessionService::get("last-group-id")));
    $aDataScript["pledge_ref"]=SessionService::get("last-pledge-ref");
    $group->hydrateFromBDD(array("*"));
    $aDataScript["group"]=$group;
    SessionService::set("last-group-id",0);
    SessionService::set("last-pledge-ref","");
}