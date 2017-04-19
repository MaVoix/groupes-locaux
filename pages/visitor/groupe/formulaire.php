<?php

//liste des départements
$listDepartements=new DepartementListe();
$listDepartements->applyRules4All();
$aDataScript["departements"]=$listDepartements->getPage();

//liste des circonsciptions
if(isset($_POST["departement"])){
    $listCirconscriptions=new CirconscriptionListe();
    $listCirconscriptions->applyRules4Departement(intval($_POST["departement"]));
    $aDataScript["circonscriptions"]=  $listCirconscriptions->getPage();
}

if(isset($_GET["key"]) && isset($_GET["id"])){

    $oListeGroup=new GroupListe();
    $oListeGroup->applyRules4Key($_GET["key"],$_GET["id"]);
    $aGroups=$oListeGroup->getPage();
    if(count($aGroups)==1){
        $Group=new Group(array("id"=>$aGroups[0]["id"]));
        $Group->hydrateFromBDD(array('*'));
        $aDataScript["group"]=$Group;
        $aDataScript["nameimage"]=basename($Group->getPath_pic());
        $aDataScript["checkedengagement"]="checked";
        if($Group->getPath_pic()!=""){
            $type = pathinfo($Group->getPath_pic(), PATHINFO_EXTENSION);
            $data = file_get_contents($Group->getPath_pic());
            $aDataScript["base64image"]= 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        $aDataScript["key"]=$_GET["key"];

        //criconscription
        $listCirconscriptions=new CirconscriptionListe();
        $listCirconscriptions->applyRules4Departement(intval($Group->getDepartement()));
        $aDataScript["circonscriptions"]=  $listCirconscriptions->getPage();

        //people
        $oListePeople=new PeopleListe();
        $oListePeople->applyRules4Group($Group->getId());
        $aDataScript["people"]=[];
        $aPeoples=$oListePeople->getPage();
        $n=0;
        foreach($aPeoples as $aPeople){
            $aPeople["tel_display"]=$aPeople["tel"];
            if($aPeople["type"]=="membre"){
                $n++;
                $sKey="membre".$n;
            }else{
                $sKey="mandataire";
            }
            $aDataScript["people"][$sKey]=$aPeople;
        }


    }else{
       // header("Location: /groupe/formulaire.html");
    }

}else{


}