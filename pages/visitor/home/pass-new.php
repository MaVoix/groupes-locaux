<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Tous les champs suivi de * sont obligatoires !";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();

$nError = 0;



//mandatory fields
if (!isset($_POST["pass"]) || $_POST["pass"] == "") {
    $nError++;
    array_push($aResponse["required"], array("field" => "pass"));
}
if (!isset($_POST["pass_confirm"]) || $_POST["pass_confirm"] == "") {
    $nError++;
    array_push($aResponse["required"], array("field" => "pass_confirm"));
}

if ($nError == 0) {
    $passwordLength = strlen($_POST["pass"]);

    if ($passwordLength < ConfigService::get("passwordMinLength") OR $passwordLength > ConfigService::get("passwordMaxLength")) {
        $aResponse["message"]["text"] = sprintf("Le mot de passe doit faire entre %s et %s caractères", ConfigService::get("passwordMinLength"), ConfigService::get("passwordMaxLength"));
        $nError++;
        $aResponse["required"][] = [
            "field" => "pass"
        ];
    }
}

if ($nError == 0) {
    $password = $_POST["pass"];

    if (preg_match(ConfigService::get("passwordConstraint"), $password) !== 1) {
        $aResponse["message"]["text"] = "Votre mot de passe contient des caractères non autorisés (uniquement chiffre et lettre";
        $nError++;
        $aResponse["required"][] = [
            "field" => "pass"
        ];
    }
}


if ($nError == 0) {
    if ($_POST["pass"] != $_POST["pass_confirm"]) {
        $nError++;
        $aResponse["message"]["text"] = "Le mot de passe n'est pas identique à sa confirmation.";
        array_push($aResponse["required"], array("field" => "pass"));
        array_push($aResponse["required"], array("field" => "pass_confirm"));
    }
}
if($nError==0) {
    $user= new User(array("id"=> SessionService::get("last-user-pass-id")));
    $user->hydrateFromBDD(array("*"));
    if(strtotime($user->getKey_edit_limit())>strtotime("now")){
        $sPassword = User::encodePassword($_POST["pass"]);
        $user->setPass($sPassword);
        $user->save();
        $aResponse["message"]["type"] = "success";
        $aResponse["message"]["text"] = "Mot de passe enregistré !";
        $aResponse["redirect"] = "/home/connexion.html";
    }else{
        $aResponse["message"]["text"] = "La clé utilisée n'est plus valide !";
        $aResponse["redirect"] = "/home/reset-pwd.html?k=00";
    }
    

}




//return
$aDataScript['data'] = json_encode($aResponse);