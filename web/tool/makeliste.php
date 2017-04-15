<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
    require_once "../../config.php";

    $bAccesScriptAutorise = true;

    if( $bAccesScriptAutorise ) {
        if (isset($_POST["table"])) {
            $_GET = $_POST;
        }

        if (!isset($_GET["table"])) {
            $_GET["table"] = "maclass";
        }

        $sPrimaryKey = "";
        if (!empty($_GET['primarykey'])) {
            $sPrimaryKey = $_GET['primarykey'];
            $aPrimary = explode(":", $sPrimaryKey);
            $sTypePrimary = substr($aPrimary[1], 0, 1);
            $sPrimary = $aPrimary[0];
            $sVarPrimary = $sTypePrimary . ucwords($sPrimary);
        }

        if( isset($_GET["vars"]) )
        {
            $aVars = explode(",", $_GET["vars"]);
            $aFields = array();
            foreach ($aVars as $n => $sVar)
            {
                $aVar = explode(":", $sVar);
                $aFields[] = $aVar[0];
            }
        }
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <title>Document sans nom</title>
        </head>
        <body>
        <pre>
        /**
        * Class <?php echo ucfirst($_GET["table"]) . "Liste"; ?>

        */
        class <?php echo ucfirst($_GET["table"]) . "Liste extends Liste"; ?>

        {

            /**
            * Champs de la table
            */
            private static $_champs = array(
<?php
        if( count($aFields)>0 )
        {
            foreach( $aFields as $n=>$sField )
            {
                echo "              " . '"' . $sField . '"';
                if( $n+1<count($aFields) )
                {
                    echo ",";
                }
                echo "\n";
            }
        }
?>
            );

            /**
            * Constructeur
            * @param array $aParam tableau de parametres
            */
            public function __construct( $aParam=array() )
            {
                parent::__construct();
                $this->setTablePrincipale("<?= $_GET["table"]; ?>");
                $this->setAliasPrincipal("<?= ucfirst($_GET["table"]); ?>");
                /*$this->setTri("");
                $this->setSens("DESC");
                $this->setSearchFields(array(
                array("field"=>"nom")
                ))*/
            }

	    /**
	    * Access champs table
	    */
	    public static function champs()
	    {
		return self::$_champs;
	    }

	    /**
	    * Methode pour récupérer tous les champs
	    */
<?php
    echo "            public function setAllFields()";
    echo "\n            {";
    echo "\n";
    echo '                $this->setFields(self::$_champs);';
    echo "\n            }";
    echo "\n\n";
?>
        }
    </pre></body>
    </html>
<?php
    }
