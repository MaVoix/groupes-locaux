<?php
$bMaintenance=false;

$aIp=array();
$aIp["local"]                 ="127.0.0.1";
$aIp["local2"]                ="::1";

if(in_array($_SERVER['REMOTE_ADDR'],$aIp)){
    $bMaintenance=false;
}



