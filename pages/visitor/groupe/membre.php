<?php
$num=intval($_POST["maxnum"])+1;
if($_POST["nbitem"]<=ConfigService::get("member-max")){
    $aDataScript["num"]=$num;
}else{
    $aDataScript["num"]=0;
}


