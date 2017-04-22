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

        //user
        $oListeUser=new UserListe();
        $oListeUser->applyRules4Group($Group->getId());
        $aDataScript["user"]=[];
        $aUsers=$oListeUser->getPage();
        $n=0;
        foreach($aUsers as $aUser){
            $aUser["tel_display"]=$aUser["tel"];
            if($aUser["type"]=="membre"){
                $n++;
                $sKey="membre".$n;
            }else{
                $sKey="mandataire";
            }
            $aDataScript["user"][$sKey]=$aUser;
        }


    }else{
       // header("Location: /groupe/formulaire.html");
    }

}else{


}