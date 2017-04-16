<?php

//liste des départements
$listDepartements=new DepartementListe();
$listDepartements->applyRules4All();
$aDataScript["departements"]=$listDepartements->getPage();