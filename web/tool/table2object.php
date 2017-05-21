<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Document sans nom</title>
</head>

<body>
<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once "../../config.php";
require_once "../../services/ConfigService.class.php";
require_once "../../services/SessionService.class.php";
require_once "../../services/Mysql.class.php";
require_once "../../services/MysqlStatement.class.php";

function getTable($s){
    return $s;
}

    $aSqlParam = array();
    $aSqlParam["host"]			=	ConfigService::get("bdd-serveur");
    $aSqlParam["login"]			=	ConfigService::get("bdd-login");
    $aSqlParam["pass"]			=	ConfigService::get("bdd-pass");
    $aSqlParam["base"]			=	ConfigService::get("bdd-base");

    //CREER LA BASE
    $oDb = new Mysql($aSqlParam);

    $oDb->select("DESCRIBE `". getTable( $_GET["table"]."`" ));
    $aFields = $oDb->getRes();

    $sLiens = "";
    $sPrimaryKey = "";

    foreach( $aFields as $aField )
    {
        //print_r( $aField );
        //echo "<hr />";

	    $sFieldType = $aField['Type'];

	    // retrait unsigned
	    $sFieldType = str_replace("unsigned", "", $sFieldType);

	    // clean
	    $sFieldType = trim( $sFieldType );

        $sTinyIntBoolReg = "/tinyint\\(1\\)/";
        if( preg_match($sTinyIntBoolReg, $sFieldType )==1 )
        {
            $sType = "bool";
        }
        else
        {
            $sType = preg_replace("/\\(.+\\)/", "", $sFieldType);
            $sType = trim( $sType );
            $sType = strtolower( $sType );
        }

        $bPrimaryKey = $aField['Key']=="PRI" ? true : false;

        if( $sType=="int" || $sType=="tinyint" )
        {
            $sType = "number";
        }

        if( in_array($sType, array("varchar", "text", "mediumtext", "enum", "date", "datetime", "char") ) )
        {
            $sType = "string";
        }

        if( strlen($sLiens)>0 )
        {
            $sLiens .= ",";
        }

        $sLiens .= $aField["Field"].":".$sType;

        if( $bPrimaryKey )
        {
            $sPrimaryKey = $aField["Field"] .":".  $sType;
        }
    }

    $table = $_GET["table"];

    echo <<<EOF
<form method="GET" action="setget.php" target="_blank">
	<input type="hidden" name="table" value="{$table}" />
	<input type="hidden" name="vars" value="{$sLiens}" />
	<input type="hidden" name="primarykey" value="{$sPrimaryKey}" />
	<div style="display: none;">
		Méthodes personnalisées : <br /><br /><textarea name="persomethods" style="width: 100%; height: 800px;"></textarea>
	</div>
	<br /><input type="submit" value="Générer classe métier" />
</form>

<form method="GET" action="makeliste.php" target="_blank">
	<input type="hidden" name="table" value="{$table}" />
	<input type="hidden" name="vars" value="{$sLiens}" />
	<input type="hidden" name="primarykey" value="{$sPrimaryKey}" />
	<br /><input type="submit" value="Générer liste" />
</form>
EOF;

    //echo "<br /><a href=\"setget.php?table=". $_GET["table"] ."&vars=". $sLiens ."&primarykey=". $sPrimaryKey ."\" target=\"_blank\">Ouvrir la class</a>";

?>
</body>
</html>
