<?php

$_CONFIG=array();

// dev/prod features

$_CONFIG["twig_auto_reload"]                = true; //set true to disable cache twig
$_CONFIG["js_auto_reload"]                  = true; //set true to disable cache js
$_CONFIG["css_auto_reload"]                 = true; //set true to disable cache css
$_CONFIG["urlSite"]                         = "http://groupeslocaux";
$_CONFIG["urlSiteDons"]                     = "http://tirelire";
$_CONFIG["key"]                             = "changeit"; //string for generate key links

// mysql
$_CONFIG["bdd-login"]			            = "root";
$_CONFIG["bdd-pass"]			            = "";
$_CONFIG["bdd-base"]			            = "groupes-locaux";
$_CONFIG["bdd-serveur"]			            = "localhost";

//mail
$_CONFIG["mail-expediteur-mail"]	      ="contact@mavoix.info";
$_CONFIG["mail-expediteur-nom"]		      ="MAVOIX";
$_CONFIG["mail-bcc"]				            =array();
$_CONFIG["mail-reply-mail"]			        ="contact@mavoix.info";
$_CONFIG["mail-reply-nom"]			        ="MAVOIX";
$_CONFIG["mail-isSMTP"]				          =false; //server smtp enable (use php mail function if false)
$_CONFIG["mail-smtp-serveur"]		        =""; //server smtp
$_CONFIG["mail-smtp-port"]		          =465; //port server smtp
$_CONFIG["mail-smtp-login"]			        =""; //server smtp login
$_CONFIG["mail-smtp-pass"]			        =""; //server smtp pass

//main
$_CONFIG["idSite"]                          = "collectedon"; //for unique session var prefix
$_CONFIG["types"]			                = "visitor|donor>visitor|mandataire>donor>visitor|admin>mandataire>donor>visitor";
if(strstr($_SERVER["HTTP_HOST"],"collectifs")){
    $_CONFIG["navigation"]                      ="groupes";
    $_CONFIG["area-default"]                    = array("visitor"=>"groupe","mandataire"=>"groupe","admin"=>"groupe");
    $_CONFIG["page-default"]                    = array("visitor"=>"groupes","mandataire"=>"suivi-promesse","admin"=>"liste");
  }else{
      $_CONFIG["navigation"]                      ="dons";
      $_CONFIG["area-default"]                    = array("visitor"=>"dons","mandataire"=>"dons","admin"=>"groupe");
      $_CONFIG["page-default"]                    = array("visitor"=>"accueil","mandataire"=>"suivi-promesse","admin"=>"liste");
  }
$_CONFIG["format-default"]                  = "html";
$_CONFIG["max-filesize"]                    = 8; // in Mb
$_CONFIG["max-width"]                       = 4000; // in PX
$_CONFIG["max-height"]                      = 4000; // in PX
$_CONFIG["min-width"]                       = 600; // in PX
$_CONFIG["min-height"]                      = 600; // in PX
$_CONFIG["mime-type-limit"]                 = array('image/jpeg'=>'jpg','image/png'=>'png');
$_CONFIG["member-max"]                      = 10;
$_CONFIG["passwordMinLength"]               = 6;
$_CONFIG["passwordMaxLength"]               = 30;
$_CONFIG["passwordConstraint"]              = "/^[a-z0-9]+$/i";
