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
    $datagroup["candidat"]=$group->getCandidat();
    $datagroup["path_pic_fit"]=$group->getPath_pic_fit();
    //protection division par 0
    if($group->getAmount_target()==0){
        $group->setAmount_target(1);
    }
    $datagroup["pledge_amount"]=$group->getAmount_plegde();
    $datagroup["income_amount"]=$group->getAmount_income();

    $datagroup["pledge_percent"]= round($datagroup["pledge_amount"]*100/ $group->getAmount_target());
    $datagroup["income_percent"] = round($datagroup["income_amount"]*100/ $group->getAmount_target());
    if($datagroup["income_percent"]>100){
        $datagroup["income_percent"]=100;
        $datagroup["pledge_percent"]=0;
    }
    if($datagroup["pledge_percent"]+$datagroup["income_percent"]>100){
        $datagroup["pledge_percent"]=100-$datagroup["income_percent"];
    }

    //assure un affichage minimum de 12% pour la barre de progression income (même si 0)
    if($datagroup["income_percent"]<12){
        $datagroup["income_percent"]=12;
    }
    //assure un affichage maximum de 88% pour la barre de progression pledge (même si 100%)
    if($datagroup["pledge_percent"]>88){
        $datagroup["pledge_percent"]=88;
    }
    //clé par code de département puis code de circo
    //$groupsOut[$oDepartement->getCode()."-".$oCirconscription->getCode()."-".$group->getId()]=$datagroup;
    //clé par montant restant
    $groupsOut[str_pad(round($group->getAmount_target()-$datagroup["pledge_amount"]-$datagroup["income_amount"]),6,"0",STR_PAD_LEFT)."-".$group->getId()]=$datagroup;

}
krsort($groupsOut);
$aDataScript["groups"]=$groupsOut;