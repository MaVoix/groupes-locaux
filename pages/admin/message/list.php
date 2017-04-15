<?php
$aDataScript=array();

$aList = MessageService::getListToArray();

$aDataScript['messages'] = $aList['list'];
$aDataScript['numbers'] = $aList['numbers'];
$aDataScript['max'] = $aList['max'];
