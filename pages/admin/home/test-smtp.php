<?php

global $_CONFIG;
$_CONFIG["mail-isSMTP"]				        =true; //server smtp enable (use php mail function if false)
$_CONFIG["mail-smtp-serveur"]		        ="XXXX"; //server smtp
$_CONFIG["mail-smtp-login"]			        ="XXXX"; //server smtp login
$_CONFIG["mail-smtp-pass"]			        ="XXXXX"; //server smtp pass
$_CONFIG["mail-smtp-port"]			        =25; //port smtp
Mail::sendMail("clement@k-mikaze.com", "AVEC SMTP", "OK HTML", "OK TXT", true);
echo "ok";