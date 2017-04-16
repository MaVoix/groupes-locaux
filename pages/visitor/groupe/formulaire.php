<?php

//liste des départements
$listDepartements=new DepartementListe();
$listDepartements->applyRules4All();
$aDataScript["departements"]=$listDepartements->getPage();

//liste des circonsciptions
if(isset($_POST["departement"])){ //todo : charger la bonne liste de circo en cas d'édition
    $listCirconscriptions=new CirconscriptionListe();
    $listCirconscriptions->applyRules4Departement(intval($_POST["departement"]));
    $aDataScript["circonscriptions"]=  $listCirconscriptions->getPage();
}