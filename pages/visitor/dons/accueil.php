<?php

$oListeGroup=new GroupListe();
$oListeGroup->applyRules4ListVisitor();
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
    $datagroup["path_pic_fit"]=$group->getPath_pic_fit();
    //protection division par 0
    if($group->getAmount_target()==0){
        $group->setAmount_target(1);
    }
    $datagroup["pledge_amount"]=2500;
    $datagroup["pledge_percent"]= round($datagroup["pledge_amount"]*100/ $group->getAmount_target());
    $datagroup["income_amount"]=1250;
    $datagroup["income_percent"] = round($datagroup["income_amount"]*100/ $group->getAmount_target());
    if($datagroup["income_percent"]>100){
        $datagroup["income_percent"]=100;
        $datagroup["pledge_percent"]=0;
    }
    if($datagroup["pledge_percent"]+$datagroup["income_percent"]>100){
        $datagroup["pledge_percent"]=100-$datagroup["income_percent"];
    }
    $groupsOut[$oDepartement->getCode()."-".$oCirconscription->getCode()."-".$group->getId()]=$datagroup;

}
ksort($groupsOut);
$aDataScript["groups"]=$groupsOut;