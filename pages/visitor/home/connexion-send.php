<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Identifiants incorrects !";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();

$nError = 0;

$oMe->setEmail("xxx@xxx.xxx");
$oMe->setType("visitor");

SessionService::set("user-email","");
SessionService::set("user-type","visitor");
SessionService::set("user-id",0);

//mandatory fields
if (!isset($_POST["email"]) || $_POST["email"] == "") {
    $nError++;
    array_push($aResponse["required"], array("field" => "email"));
}
if (!isset($_POST["pass"]) || $_POST["pass"] == "") {
    $nError++;
    array_push($aResponse["required"], array("field" => "pass"));
}
if($nError==0) {

    //encode PASS
    $sPassword=User::encodePassword($_POST["pass"]);

    //CONNEXION
    $oListeUser = new UserListe();
    $oListeUser-> applyRules4Connexion($_POST["email"],  $sPassword);
    $aUsers = $oListeUser->getPage();
    if(count($aUsers)!=1){
        $nError++;
    }else{
        $aUser=$aUsers[0];
        if($aUser["email"]==$_POST["email"]  &&  $aUser["pass"]==$sPassword ){
            $oMe->setEmail($_POST["email"]);
            $oMe->setType( $aUser["type"] );

            SessionService::set("user-email",$aUser["email"]);
            SessionService::set("user-type",$aUser["type"]);
            SessionService::set("user-id",$aUser["id"]);

            if($aUser["type"]=="admin"){
                $aResponse["redirect"] = "/groupe/list.html";
            }else{
                $aResponse["redirect"] = "/groupe/accueil.html";
            }

            $aResponse["durationMessage"] = "2000";
            $aResponse["durationRedirect"] = "2000";
            $aResponse["durationFade"] = "10000";
            $aResponse["message"]["title"] = "";
            $aResponse["message"]["type"] = "success";
            $aResponse["message"]["text"] = "Connexion réussie !";

        }else{
            $nError++;
        }

    }

}




//return
$aDataScript['data'] = json_encode($aResponse);