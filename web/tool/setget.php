<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

if(isset($_POST["table"])){
	$_GET=$_POST;

}
if(!isset($_GET["table"]))
{
	$_GET["table"]="maclass";
}

$sPrimaryKey = "";
if( !empty($_GET['primarykey']) )
{
	$sPrimaryKey = $_GET['primarykey'];
	$aPrimary = explode(":", $sPrimaryKey);
	$sTypePrimary = substr( $aPrimary[1], 0, 1 );
	$sPrimary = $aPrimary[0];
	$sSetMethodePrimary = "set".ucwords($sPrimary);
	$sGetMethodePrimary = "get".ucwords($sPrimary);
	$sVarPrimary = $sTypePrimary.ucwords($sPrimary);
}
?>
<?php if(!isset($_GET["write-only"])){ ?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Document sans nom</title>
	</head>
	<body>
	<pre><?php echo "&lt;?php"; }else{  echo "<?php"; } ?>
	/**
	* Class <?php echo ucfirst($_GET["table"]); ?>

	*/
	class <?php echo ucfirst($_GET["table"]); ?>
	{
<?php

require_once "../../config.php";


	$aVars=explode(",",$_GET["vars"]);
	echo "\n\tprivate \$aDataNonHydrate = array();";
	echo "\n\tprivate \$aDataSet = array();";
	echo "\n\tprivate \$callHydrateFromBDDOnGet = 0;\n";
	echo "\n\tprivate \$_sDbInstance = null;\n";
	foreach($aVars as $sVar)
	{
		$aVar=explode(":",$sVar);
		$sTypeVar=substr($aVar[1],0,1);
		if($sTypeVar=="i" || $sTypeVar=="d" || $sTypeVar=="f")
		{
			$sTypeVar="n";
		}
		$sVarname="\$".$sTypeVar."".ucwords($aVar[0]);
		echo "\n\tprivate ".$sVarname.";";
	}
	echo "\n";
	?>


	/**
	* Constructeur
	* @param array $aParam tableau de parametres ( clé "id" pour instancier un <?php echo strtolower($_GET["table"]); ?> avec un id précis )
	* @param $sDbInstance (Opt) Nom de l'instance de la bdd à utiliser
	*/
	public function __construct( $aParam=array(), $sDbInstance=null )
	{
	$this->hydrate($aParam);
	<?php if( empty($sPrimaryKey) ): ?>
	if(isset($aParam["id"]))
	{
	$this->setId($aParam["id"]);
	}
	else
	{
	$this->setId(0);
	}
<?php else: ?>
	$this-><?=$sVarPrimary?> = ( isset($aParam['<?=$sPrimary?>']) ) ? $aParam['<?=$sPrimary?>'] : <?=($sTypePrimary=="n")?"0":"null"?>;
<?php endif; ?>
	$this->_sDbInstance = $sDbInstance;
	}

	/**
	* Fonction permettant d'hydrater un objet
	* @param $aDonnees array tableau clé-valeur à hydrater ( par exemple "nom"=>"DUPONT" )
	*/
	public function hydrate($aDonnees)
	{
	foreach ($aDonnees as $sKey => $sValue)
	{
	if(!is_int($sKey))
	{
	$sMethode = 'set'.ucfirst($sKey);
	if (method_exists($this, $sMethode))
	{
	if( is_null($sValue) ) $sValue="";
	$this->$sMethode($sValue);
	}
	else
	{
	//echo "&lt;br /&gt;<?php echo ucfirst($_GET["table"]); ?>->$sMethode() n'existe pas!";
	$this->addDataNonHydrate($sKey,$sValue);
	}
	}
	}
	}

	/**
	* Fonction permettant d'hydrater un objet à partir d'une liste de champs (s'appuie sur l'id de l'objet)
	* @param $aFields array tableau contenant la liste des champs à hydrater ( '*' pour tous)
	*/
	public function hydrateFromBDD($aFields=array())
	{
	if(count($aFields))
	{
	//hydrate uniquement les champs de base (pour le reste coder directement dans les acesseurs)
	$aData=DbLink::getInstance($this->_sDbInstance)->selectForHydrate($this-><?=$sGetMethodePrimary."()"?>,"<?php  echo $_GET["table"]; ?>",$aFields);

	//hydrate l'objet
	$this->hydrate($aData);
	}
	}


	/**
	* Fonction permettant d'ajouter des données non-hydratées à l'objet
	* @param string $sKey champs
	* @param mixed $sValue valeur
	*/
	public function addDataNonHydrate($sKey,$sValue)
	{
	$this->aDataNonHydrate[$sKey]=$sValue;
	}

	/**
	* Fonction permettant de récuperer des données non-hydratées à l'objet
	* @param string $sKey champs à récupérer
	* @return string valeur du champ
	*/
	public function getDataNonHydrate($sKey)
	{
	if(isset($this->aDataNonHydrate[$sKey]))
	{
	return $this->aDataNonHydrate[$sKey];
	}
	else
	{
	return "";
	}
	}

	/**
	* Fonction permettant de supprimer fictivement un objet (en lui passant un date supprime)
	*/
	public function supprime()
	{
	$this->setDate_deleted(date("Y-m-d H:i:s"));
	$this->save();
	}

	/**
	* Fonction permettant de supprimer réellement un objet (en faisant un DELETE )
	*/
	public function delete()
	{
	<?php
	echo '$oReq=DbLink::getInstance($this->_sDbInstance)->prepare(\'DELETE FROM \'."'.$_GET["table"].'".\' WHERE  id=:id \');';
	?>

	$oReq->execute(array("id"=>$this->getId()));
	$this->vide();
	}

	/**
	* Consulte la base de données pour savoir si l'objet existe, en le recherchant par son id
	* @static
	* @param int $nId Id de l'objet à chercher
	* @param $sDbInstance (Opt) Nom de l'instance de la bdd
	* @return bool Vrai si l'objet existe, Faux sinon
	*/
	public static function exists($nId=0, $sDbInstance=null)
	{
	<?php
	echo '$oReq=DbLink::getInstance($sDbInstance)->prepare(\'SELECT id FROM \'."'.$_GET["table"].'".\' WHERE  id=:id \');';
	?>

	$oReq->execute(array("id"=>$nId));
	$aRes=$oReq->getRow(0);
	return (count($aRes)!=0);
	}

	/**
	* Sauvegarde l'objet en base
	*/
	<?php

	echo 'public function save()' . "\n\t{";
	echo "\n\t\t\$aData=array();";
	foreach($aVars as $sVar){
		$aVar=explode(":",$sVar);
		$sTypeVar=substr($aVar[1],0,1);
		if($sTypeVar=="i" || $sTypeVar=="d" || $sTypeVar=="f" || $sTypeVar=="b"){
			$sTypeVar="n";
			$sValueVar='0';
		}else{
			$sValueVar="''";
		}
		if($aVar[0]!=$sPrimary)
		{
			$sVarname="get".ucwords($aVar[0])."()";
			echo "\n\t\tif(isset(\$this->aDataSet[\"".$aVar[0]."\"]))\n\t\t{\n\t\t\t\$aData[\"".$aVar[0]."\"]=\$this->".$sVarname.";\n\t\t}\n";
		}
	}
	echo "\n\t\tif(\$this->". $sGetMethodePrimary."()" .">0)\n\t\t{";
	echo "\n\t\t\t". 'DbLink::getInstance($this->_sDbInstance)'."->update(\"".$_GET["table"]."\",\$aData,' ". $sPrimary ."=\"'.\$this->". $sGetMethodePrimary."()" .".'\" ');";
	echo "\n\t\t}";
	echo "\n\t\telse";
	echo "\n\t\t{";
	echo "\n\t\t\t\$this->". $sSetMethodePrimary ."(DbLink::".'getInstance($this->_sDbInstance)'."->insert(\"".$_GET["table"]."\",\$aData));	";
	echo "\n\t\t}";
	echo "\n\t\t\$this->aDataSet=array();";
	echo "\n\t}";



	?>


	/**
	* Deshydrate complement l'objet, et vide la liste des champs à sauvegarder
	*/
	<?php
	echo "private function vide()\n\t{";
	echo "\n\t\t\$this->callHydrateFromBDDOnGet=0;";
	echo "\n\t\t\$this->aDataSet=array();";
	foreach($aVars as $sVar){
		$aVar=explode(":",$sVar);
		$sTypeVar=substr($aVar[1],0,1);
		if($sTypeVar=="i" || $sTypeVar=="d" || $sTypeVar=="f" || $sTypeVar=="b"){
			$sTypeVar="n";
			$sValueVar='0';
		}else{
			$sValueVar="NULL";
		}
		if($aVar[0]!=$sPrimary)
		{
			$sVarname="set".ucwords($aVar[0])."(".$sValueVar.")";
			echo "\n\t\t\$this->".$sVarname.";";
		}
	}
	echo "\n\t}\n\n";

	?>
	<?php
	$aChamps = [];

	foreach( $aVars as $n=>$sVar )
	{
		$aChamp = explode(":", $sVar);
		$sChamp = $aChamp[0];
		$aChamps[] = $sChamp;
	}

	$nNbChamps = count($aChamps);
	?>
	/**
	* Renvoie l'objet sous forme de chaine de caractère
	*/
	public function __toString()
	{
	$aObjet = [<?php
	foreach( $aChamps as $n=>$sChamp )
	{
		$sGetMethode = "get". ucwords($sChamp) ."()";
		echo "\n\t\t". '"'. $sChamp .'" => $this->'. $sGetMethode;

		if( $n+1<$nNbChamps )
		{
			echo ",";
		}
		else echo "\n\t";
	}
	?>];

	return json_encode($aObjet);
	}





	<?php

	foreach($aVars as $sVar){
		$aVar=explode(":",$sVar);
		$sTypeVar=substr($aVar[1],0,1);
		if($sTypeVar=="i" || $sTypeVar=="d" || $sTypeVar=="f"){
			$sTypeVar="n";
			$aVar[1]="numeric";
		}
		$sVarname="\$".$sTypeVar."".ucwords($aVar[0]);
		echo "\n\n\n";
		echo "\t/**";
		echo "\n\t* Set le champ ".strtolower(substr($sVarname,2))." ";
		echo "\n\t* @param ".$aVar[1]." ".$sVarname." nouvelle valeur pour le champ ".strtolower(substr($sVarname,2))." ";
		echo "\n\t*/";
		echo "\n\tpublic function set".ucwords($aVar[0])."($sVarname)\n\t{";
		$sTypeFunc=$aVar[1];
		$sAddTypeSupp="";
		if($sTypeFunc=="number")
		{
			$sTypeFunc="numeric";
		}
		if($sTypeFunc=="bool")
		{
			$sAddTypeSupp=" ||  $sVarname==1 || $sVarname==0";
		}
		echo "\n\t\tif( is_null($sVarname) ) $sVarname='';";
		if($sTypeFunc=="numeric" || $sTypeFunc=="bool"){
			echo "\n\t\tif( is_".$sTypeFunc."($sVarname) ".$sAddTypeSupp." || $sVarname=='' )\n\t\t{";
		}else{
//				echo "\n\t\tif( is_".$sTypeFunc."($sVarname) ".$sAddTypeSupp." )\n\t\t{";
		}
		echo "\n\t\t\t\$this->".str_replace("\$","",$sVarname)." = ".$sVarname.";";
		echo "\n\t\t\t\$this->aDataSet[\"".$aVar[0]."\"]=1;";

		if($sTypeFunc=="numeric" || $sTypeFunc=="bool")
		{
			echo "\n\t\t}";
		}

		echo "\n\t}\n";

		echo "\n\n\n";
		echo "\t/**";
		echo "\n\t* Get le champ ".strtolower(substr($sVarname,2))." ";
		echo "\n\t* @return ".$aVar[1]." valeur du champ ".strtolower(substr($sVarname,2))." ";
		echo "\n\t*/";
		echo "\n\tpublic function get".ucwords($aVar[0]).'()'."\n\t{";
		echo "\n\t\tif( !is_null(\$this->".str_replace("\$","",$sVarname).") )\n\t\t{";
		echo "\n\t\t\tif( \$this->".str_replace("\$","",$sVarname)."==='' )\n\t\t\t{";
		echo "\n\t\t\t\t return NULL;";
		echo "\n\t\t\t}\n\t\t\telse\n\t\t\t{ ";
		echo "\n\t\t\t\t return \$this->".str_replace("\$","",$sVarname).";";
		echo "\n\t\t\t}";
		echo "\n\t\t}\n\t\telse\n\t\t{";
		echo "\n\t\t\t\$this->hydrateFromBDD(array('".substr(strtolower($sVarname),2)."'));";
		echo "\n\t\t\t\$this->callHydrateFromBDDOnGet++;";
		echo "\n\t\t\tif(\$this->callHydrateFromBDDOnGet>10)\n\t\t\t{";
		echo "\n\t\t\t\techo \"&lt;br /&gt;WARNING : trop d'appel en base depuis l'accesseur \". __CLASS__ .\"::\". __FUNCTION__ .\"\";";
		echo "\n\t\t\t}";
		echo "\n\t\t\treturn \$this->".str_replace("\$","",$sVarname).";";
		echo "\n\t\t}";
		echo "\n\t}\n";

		if((strstr($sVarname,"_id") || strstr($sVarname,"Id_"))){
			$sNomClass=str_replace("_id","",$aVar[0]);
			$sNomClass=str_replace("id_","",$sNomClass);
			$sNomClass=ucwords($sNomClass);
			if($sNomClass!="Kzam"){
				echo "\n\tpublic function ".$sNomClass.'()'."\n\t{";
				echo "\n\t\tif( \$this->".str_replace("\$","",$sVarname)."){";
				echo "\n\t\t\t\$o".$sNomClass." = new ".$sNomClass."(array(\"id\"=>\$this->".str_replace("\$","",$sVarname)."));";
				echo "\n\t\t\t\$o".$sNomClass."->HydrateFromBDD(array(\"*\"));";
				echo "\n\t\t\treturn \$o".$sNomClass.";";
				echo "\n\t\t}else{";
				echo "\n\t\t\treturn NULL;";
				echo "\n\t\t}";
				echo "\n\t}";
			}

		}
	}
	?>

    /*
    ********************************************************************************************
    *                             DEBUT FONCTIONS PERSONNALISES                  	           *
    ********************************************************************************************
    */

<?php



?>

    /*
    ********************************************************************************************
    *                             FIN FONCTIONS PERSONNALISES                     	           *
    ********************************************************************************************
    */


    } <?php if(!isset($_GET["write-only"])){ echo "" ?></pre></body>
	</html><?php } else { echo ""; }?>