<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"] = "Erreur";
$aResponse["message"]["type"] = "error";
$aResponse["message"]["text"] = "Tous les champs suivi de * sont obligatoires !";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();

$nError = 0;

//check edit KEY
$bEdit = false;
$OldGroup = new Group();
if (isset($_POST["id"]) && isset($_POST["key"])) {
    $oListeGroup = new GroupListe();
    $oListeGroup->applyRules4Key($_POST["key"], $_POST["id"]);
    $aGroups = $oListeGroup->getPage();
    if (count($aGroups) == 1) {
        $bEdit = true;
        $OldGroup = new Group(array("id" => $aGroups[0]["id"]));
        $OldGroup->hydrateFromBDD(array('*'));
    } else {

        $nError++;
        $aResponse["message"]["text"] = "Impossible de modifier ce groupe.";
    }
}


//mandatory fields
$aMandoryFields = array("group_name", "departement", "circonscription");
if (!$bEdit) {
    $aMandoryFields[] = "mandataire_pass";
    $aMandoryFields[] = "mandataire_pass_confirm";
}

//ajoute les engagements si l'utilisateur n'est pas admin
if ($oMe->getType() != "admin") {
    $aEngagements = array();
    //verification des engagements 1 à 17
    for ($i = 2; $i <= 17; $i++) {
        $aEngagements[] = "engagement-a" . $i;
    }
    $aMandoryFields = array_merge($aMandoryFields, $aEngagements);
}


foreach ($aMandoryFields as $sField) {
    if (!isset($_POST[$sField]) || $_POST[$sField] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => $sField));
        $_POST[$sField] = "";
    }
}


//parcours des users
$fieldsUser = ["user_id", "user_delete", "user_civility", "user_name", "user_firstname", "user_tel", "user_email", "user_ad1", "user_ad2", "user_ad3", "user_zipcode", "user_city"];
$fieldsUserMandatory = ["user_name", "user_civility", "user_firstname", "user_tel", "user_email", "user_ad1", "user_zipcode", "user_city"];
$nUserMax = ConfigService::get("member-max"); // nombres de user MAX (hors mandataire)

$aUsersMember = [];
for ($n = 0; $n <= $nUserMax + 1; $n++) {
    if ($n == 0) {
        $aUsersMember["mandataire"] = [];
        $aUsersMember["mandataire"]["type"] = "mandataire";
    } elseif ($n == 1) {
        $aUsersMember["candidat"] = [];
        $aUsersMember["candidat"]["type"] = "candidat";
    } else {
        $aUsersMember["membre" . $n] = [];
        $aUsersMember["membre" . $n]["type"] = "membre";
    }
}

foreach ($fieldsUser as $field) {
    if (isset($_POST[$field])) {
        foreach ($_POST[$field] as $type => $value) {

            if (in_array($field, $fieldsUserMandatory) && $_POST[$field][$type] == "" && $type!="candidat") {
                $nError++;
                array_push($aResponse["required"], array("field" => $field . "\\[" . $type . "\\]"));
                if ($field == "user_tel") {
                    array_push($aResponse["required"], array("field" => "user_tel_display" . "\\[" . $type . "\\]"));
                }
            } else {
                $aUsersMember[$type][str_replace("user_", "", $field)] = $value;
            }
            //verification saisie
            if ($field == "user_email" && $_POST[$field][$type] != "") {
                if (!filter_var($_POST[$field][$type], FILTER_VALIDATE_EMAIL)) {
                    $aResponse["message"]["text"] = "L'adresse e-mail est incorrecte.";
                    array_push($aResponse["required"], array("field" => "user_email" . "\\[" . $type . "\\]"));
                    $nError++;
                }
            }

        }
    } else {
        if (in_array($field, $fieldsUserMandatory)) {
            $nError++;
            array_push($aResponse["required"], array("field" => $field . "\\[" . $type . "\\]"));
            if ($field == "user_tel") {
                array_push($aResponse["required"], array("field" => "user_tel_display" . "\\[" . $type . "\\]"));
            }
        }
    }
}

//verification mot de passes
if (!$bEdit) {
    if ($nError == 0) {
        $passwordLength = strlen($_POST["mandataire_pass"]);

        if ($passwordLength < ConfigService::get("passwordMinLength") OR $passwordLength > ConfigService::get("passwordMaxLength")) {
            $aResponse["message"]["text"] = sprintf("Le mot de passe doit faire entre %s et %s caractères", ConfigService::get("passwordMinLength"), ConfigService::get("passwordMaxLength"));
            $nError++;
            $aResponse["required"][] = [
                "field" => "mandataire_pass"
            ];
        }
    }

    if ($nError == 0) {
        $password = $_POST["mandataire_pass"];

        if (preg_match(ConfigService::get("passwordConstraint"), $password) !== 1) {
            $aResponse["message"]["text"] = "Votre mot de passe contient des caractères non autorisés (uniquement chiffre et lettre";
            $nError++;
            $aResponse["required"][] = [
                "field" => "mandataire_pass"
            ];
        }
    }


    if ($nError == 0) {
        if ($_POST["mandataire_pass"] != $_POST["mandataire_pass_confirm"]) {
            $nError++;
            $aResponse["message"]["text"] = "Le mot de passe n'est pas identique à sa confirmation.";
            array_push($aResponse["required"], array("field" => "mandataire_pass"));
            array_push($aResponse["required"], array("field" => "mandataire_pass_confirm"));
        }
    }
}

//vérifie si le mail est déjà utilisé
if (!$bEdit) {
    if ($nError == 0) {
        $oListeUser = new UserListe();
        $oListeUser->applyRules4SearchByEmail($_POST["user_email"]["mandataire"], "mandataire");
        $aUsersMails = $oListeUser->getPage();
        if (count($aUsersMails)) {
            $aResponse["message"]["text"] = "Un compte est déjà associé à cette adresse e-mail.";
            array_push($aResponse["required"], array("field" => "user_email" . "\\[mandataire\\]"));
            $nError++;
        }
    }
}


//verifie les informations pour la page collectif
//informations publiques pour la page collectif
if(isset($_POST["email"]) && $_POST["email"]!=""){
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $aResponse["message"]["text"] = "L'adresse e-mail de contact de votre collectif n'est pas correcte";
        array_push($aResponse["required"], array("field" => "email"));
        $nError++;
    }
}
if(isset($_POST["facebook_page"]) && $_POST["facebook_page"]!="" ){
    if (!filter_var($_POST["facebook_page"], FILTER_VALIDATE_URL)) {
        $aResponse["message"]["text"] = "L'adresse de votre page facebook n'est pas correcte";
        array_push($aResponse["required"], array("field" => "facebook_page"));
        $nError++;
    }
}
if(isset($_POST["facebook_group"]) && $_POST["facebook_group"]!="" ){
    if (!filter_var($_POST["facebook_group"], FILTER_VALIDATE_URL)) {
        $aResponse["message"]["text"] = "L'adresse de votre groupe facebook n'est pas correcte";
        array_push($aResponse["required"], array("field" => "facebook_group"));
        $nError++;
    }
}

if(isset($_POST["twitter"]) && $_POST["twitter"]!="" ){
    if (!filter_var($_POST["twitter"], FILTER_VALIDATE_URL)) {
        $aResponse["message"]["text"] = "L'adresse de votre page twitter n'est pas correcte";
        array_push($aResponse["required"], array("field" => "twitter"));
        $nError++;
    }
}


//verification IBAN
if ($nError == 0) {
    if (isset($_POST["iban"]) && $_POST["iban"] != "") {
        if (!vars::checkIBAN($_POST["iban"])) {
            $aResponse["message"]["text"] = "L'IBAN saisi ne semble pas valide.";
            array_push($aResponse["required"], array("field" => "iban"));
            $nError++;
        }
    }
}

/*
if(ConfigService::get("enable-captcha")){
    if (!isset($_POST["captcha"]) || $_POST["captcha"] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => "captcha"));
        $_POST["captcha"]="";
    }else{
        if(SessionService::get("captcha-value") !=  $_POST["captcha"]){
            $nError++;
            $aResponse["message"]["text"] = "Le code de sécurité est incorrect.";
            $_POST["captcha"]="";
        }
    }
}*/


//check upload picture
$bIsUploadedPic = false;
if (isset($_POST["imageFilename"]) && $_POST["imageFilename"] != "") {
    $aLimitMime = ConfigService::get("mime-type-limit");
    $aMime = array_keys(ConfigService::get("mime-type-limit"));

    if ($nError == 0) {
        if (!isset($_POST["imageFilename"]) || $_POST["imageFilename"] == "") {
            $nError++;
            $aResponse["message"]["text"] = "N'oubliez pas d'envoyer votre photo !";
        }
        if (!isset($_POST["imageData"]) || $_POST["imageData"] == "") {
            $nError++;
            $aResponse["message"]["text"] = "N'oubliez pas d'envoyer votre photo !";
        }
    }

    $sExtension = "jpg";
    if ($nError == 0) {
        //Add base 64 encode data in FILE "image"
        if (!isset($_FILES)) {
            $_FILES = array("image" => array());
        }
        $sExtension = strtolower(substr($_POST["imageFilename"], -3));
        if ($sExtension == "peg") {
            $sExtension = "jpg";
        }
        $_FILES["image"]["tmp_name"] = '../tmp/' . md5(rand(1000, 99999) . time() . ConfigService::get("key")) . '.' . $sExtension;
        $_FILES["image"]["name"] = $_POST["imageFilename"];
        $encodedData = explode(',', $_POST["imageData"]);
        $decodedData = base64_decode($encodedData[1]);
        file_put_contents($_FILES["image"]["tmp_name"], $decodedData);
    }

    if ($nError == 0) {
        if (!in_array(mime_content_type($_FILES['image']['tmp_name']), $aMime)) {
            $nError++;
            $aResponse["message"]["text"] = "Format de fichier de votre photo non reconnu.";
        }
    }
    if ($nError == 0) {
        if (filesize($_FILES['image']['tmp_name']) > ConfigService::get("max-filesize") * 1024 * 1024) {
            $nError++;
            $aResponse["message"]["text"] = "Votre photo dépasse le poids maximum autorisé. (" . ConfigService::get("max-filesize") . " Mb )";
        }
    }


    if ($nError == 0) {
        //format de l'image
        $img = new claviska\ SimpleImage($_FILES['image']['tmp_name']);
        if (
            $img->getWidth() < ConfigService::get("min-width") || $img->getWidth() > ConfigService::get("max-width") ||
            $img->getHeight() < ConfigService::get("min-height") || $img->getHeight() > ConfigService::get("max-height")
        ) {
            $nError++;
            $aResponse["message"]["text"] = "Les dimensions de votre photo ne sont pas valides ( entre " . ConfigService::get("min-width") . "px et " . ConfigService::get("max-height") . "px )";
        }

    }
    if ($nError == 0) {
        $bIsUploadedPic = true;
    }
}


if ($nError == 0) {
    if ($bEdit) {
        $Group = new Group(array("id" => $OldGroup->getId()));
        $Group->setDate_amended(date("Y-m-d H:i:s"));
        $OldGroup->hydrateFromBDD(array('*'));
    } else {
        $Group = new Group();
        $Group->setDate_created(date("Y-m-d H:i:s"));
        //generate key for link
        $sKey = sha1($_SERVER["REMOTE_ADDR"] . ConfigService::get("key") . rand(1000, 9999) . time());
        $Group->setKey_edit($sKey);
        $Group->setState("offline");
    }

   if (isset($_POST["autovalid"]) && $_POST["autovalid"] == "1") {
            $Group->setState("online");
        }

    $Group->setName($_POST["group_name"]);
    //todo : vérifier les ID avant sauvegarde
    $Group->setDepartement_id(intval($_POST["departement"]));
    $Group->setCirconscription_id(intval($_POST["circonscription"]));

    //informations publiques pour la page collectif
    if(isset($_POST["email"]) && $_POST["email"]!="" ){
      $Group->setEmail($_POST["email"]);
    }
    if(isset($_POST["facebook_page"]) && $_POST["facebook_page"]!=""){
        $Group->setFacebook_page($_POST["facebook_page"]);
    }
    if(isset($_POST["facebook_group"]) && $_POST["facebook_group"]!=""){
        $Group->setFacebook_group($_POST["facebook_group"]);
    }
    if(isset($_POST["twitter"]) && $_POST["twitter"]!=""){
        $Group->setTwitter($_POST["twitter"]);
    }


    //information bank
    if (isset($_POST["bank_name"])) {
        $Group->setBank_name($_POST["bank_name"]);
    }
    if (isset($_POST["iban"])) {
        $Group->setIban($_POST["iban"]);
    }
    if (isset($_POST["bic"])) {
        $Group->setBic($_POST["bic"]);
    }
    if (isset($_POST["bank_city"])) {
        $Group->setBank_city($_POST["bank_city"]);
    }
    if (isset($_POST["ballots"])) {
        $Group->setBallots(intval($_POST["ballots"]));
    }
    if (isset($_POST["professions_de_foi"])) {
        $Group->setProfessions_de_foi(intval($_POST["professions_de_foi"]));
    }
    if (isset($_POST["posters"])) {
        $Group->setPosters(intval($_POST["posters"]));
    }
    if (isset($_POST["amount_target"])) {
        $Group->setAmount_target(intval($_POST["amount_target"]));
    }


    //save Files
    if ($bIsUploadedPic) {
        $outputDir = "data/" . date("Y") . "/" . date("m") . "/" . date("d") . "/" . time() . session_id() . "/";
        mkdir($outputDir, 0777, true);
        $outputFilePhoto = $outputDir . "original." . $sExtension;
        $outputFilePhotoFit = $outputDir . "photo-fit.jpg";
        if (@copy($_FILES['image']['tmp_name'], $outputFilePhoto)) {
            $img = new claviska\ SimpleImage($outputFilePhoto);
            $img->bestFit(800, 800);
            $img->toFile($outputFilePhotoFit, "image/jpeg", 100);
            $Group->setPath_pic($outputFilePhoto);
        } else {
            $aResponse["message"]["text"] = "Erreur lors de l'enregistrement de votre photo.";
            $nError++;
        }
        @unlink($_FILES['image']['tmp_name']);
    } else {
        $Group->setPath_pic("");
    }


    if ($nError == 0) {

        $Group->save();

        $nIdGroup = $Group->getId();

        //sauvegarde User
        $sEmail = "";

        foreach ($aUsersMember as $sType => $user) {
            if (isset($user["id"])) {
                if (intval($user["id"]) == 0) {
                    $oUser = new User();
                } else {
                    $oUser = new User(array("id" => intval($user["id"])));
                }
                $oUser->setGroup_id($nIdGroup);
                $oUser->setCivility($user["civility"]);
                $oUser->setName($user["name"]);
                $oUser->setFirstname($user["firstname"]);
                $oUser->setEmail($user["email"]);
                $oUser->setTel($user["tel"]);

                $oUser->setType($user["type"]);


                //recuperation du mail du mandataire
                if ($user["type"] == "mandataire") {
                    $oUser->setAd1($user["ad1"]);
                    $oUser->setAd2($user["ad2"]);
                    $oUser->setAd3($user["ad3"]);
                    $oUser->setZipcode($user["zipcode"]);
                    $oUser->setCity($user["city"]);
                    if (!$bEdit) {
                        $sPassword = User::encodePassword($_POST["mandataire_pass"]);
                        $oUser->setPass($sPassword);
                    }

                    $sEmail = $user["email"];
                } else {
                    if ($user["delete"] == 1) {
                        $oUser->setDate_deleted(date("Y-m-d H:i:s"));
                    }
                }

                $oUser->save();
            }


        }

        $TwigEngine = App::getTwig();
        $sBodyMailHTML = $TwigEngine->render("visitor/mail/body.html.twig", [
            "group" => $Group
        ]);
        $sBodyMailTXT = $TwigEngine->render("visitor/mail/body.txt.twig", [
            "group" => $Group
        ]);
        if (!$bEdit && $sEmail != "") {
            Mail::sendMail($sEmail, "Confirmation d'engagement de groupe local", $sBodyMailHTML, $sBodyMailTXT, true);
        }

        if ($oMe->getType() == "admin") {
            $aResponse["message"]["text"] = "Informations enregistrées correctement !";
            $aResponse["redirect"] = "/groupe/liste.html";
        } elseif ($oMe->getType() == "mandataire") {
            $aResponse["message"]["text"] = "Informations enregistrées correctement !";
            $aResponse["redirect"] = "/groupe/accueil.html";
        } else {
            $aResponse["message"]["text"] = "Félicitations !";
            $aResponse["redirect"] = "/groupe/felicitations.html";
        }

        SessionService::set("last-save-id", $Group->getId());

        $aResponse["durationMessage"] = "2000";
        $aResponse["durationRedirect"] = "2000";
        $aResponse["durationFade"] = "10000";
        $aResponse["message"]["title"] = "";
        $aResponse["message"]["type"] = "success";

        //if edit clean old file
        if ($bEdit) {
            if ($bIsUploadedPic) {
                @unlink($OldGroup->getPath_pic());
            }
            $aResponse["message"]["text"] = "Modification enregistrée !";
        } else {
            $aResponse["message"]["text"] = "Groupe enregistré !";
        }

    }

}


//return
$aDataScript['data'] = json_encode($aResponse);
