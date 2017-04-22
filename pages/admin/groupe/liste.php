<?php


$oListeGroup=new GroupListe();
$oListeGroup->applyRules4ListAdmin();
$groups=$oListeGroup->getPage();



$aDataScript["groups"]=$groups;

