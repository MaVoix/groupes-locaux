<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Un problème s'est produit lors de l'enregistrement.";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();




if(isset($_POST["id"])){
    $oListeGroup=new GroupListe();
    $oListeGroup->applyRules4GetGroupAdmin(intval($_POST["id"]));
    $aGroups=$oListeGroup->getPage();
    if(count($aGroups)==1){
        $oGroup= new Group(array("id"=>$aGroups[0]["id"]));
        if(isset($_POST["checked"])){
            $aResponse["type"] = "refresh-state-list";
            $aResponse["id"]=$aGroups[0]["id"];
            if($_POST["checked"]=='true'){
                $aResponse["class"]="online";
                $oGroup->setState("online");
            }else{
                $aResponse["class"]="offline";
                $oGroup->setState("offline");
            }


            $oGroup->save();
        }
    }
}

//return
$aDataScript['data'] = json_encode($aResponse);