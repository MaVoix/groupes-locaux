<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Adresse e-mail inconnue !";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();

$nError = 0;

//mandatory fields
if (!isset($_POST["email"]) || $_POST["email"] == "") {
    $nError++;
    array_push($aResponse["required"], array("field" => "email"));
}

if($nError==0) {


    //CONNEXION
    $oListeUser = new UserListe();
    $oListeUser-> applyRules4Password($_POST["email"]);
    $aUsers = $oListeUser->getPage();
    if(count($aUsers)!=1){
        $nError++;
    }else{
        $aUser=$aUsers[0];
        $user= new User(array("id"=>$aUser["id"]));

        $sKey=sha1(ConfigService::get("key").strtotime("now").rand(1000,9999));
        $user->setKey_edit($sKey);
        $user->setKey_edit_limit(date("Y-m-d H:i:s",time() + 4*60*60)); //4h
        $user->save();

        //envoi du mail
        $TwigEngine = App::getTwig();
        $sBodyMailHTML = $TwigEngine->render("visitor/mail/reset-pass-body.html.twig", [
            "user" =>  $user,
            "lien" =>  ConfigService::get("urlSite")."/home/reset-pwd.html?k=".$sKey

        ]);
        $sBodyMailTXT = $TwigEngine->render("visitor/mail/reset-pass-body.txt.twig", [
            "user" =>  $user,
            "lien" =>  ConfigService::get("urlSite")."/home/reset-pwd.html?k=".$sKey
        ]);

        Mail::sendMail( $user->getEmail(), "Réinitialisation de votre mot de passe", $sBodyMailHTML, $sBodyMailTXT, true);


        $aResponse["redirect"] = "/home/connexion.html";

        $aResponse["durationMessage"] = "2000";
        $aResponse["durationRedirect"] = "2000";
        $aResponse["durationFade"] = "10000";
        $aResponse["message"]["title"] = "";
        $aResponse["message"]["type"] = "success";
        $aResponse["message"]["text"] = "Message envoyé sur votre boite";

    }

}




//return
$aDataScript['data'] = json_encode($aResponse);