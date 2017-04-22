<?php


$oListeGroup=new GroupListe();
$oListeGroup->applyRules4ListAdmin();
$groups=$oListeGroup->getPage();

$groupsOut=[];
foreach($groups as $datagroup){
   $group=new Group(array("id"=>$datagroup["id"]));
   $group->hydrate($datagroup);

   $oDepartement=$group->getDepartement();
   $oCirconscription=$group->getCirconscription();
   $datagroup["departement"]=  $oDepartement;
   $datagroup["circonscription"]= $oCirconscription;
    $datagroup["mandataire"]=$group->getMandataire();
   $groupsOut[$oDepartement->getCode()."-".$oCirconscription->getCode()."-".$group->getId()]=$datagroup;

}

$aDataScript["groups"]=$groupsOut;

