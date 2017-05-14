<?php

$bFoundGroup = false;
$Group = new Group();
if (isset($_GET["id"])) {
    $Group = new Group(array("id" => intval($_GET["id"])));
    $Group->hydrateFromBDD(array('*'));
    if ($Group->getState() == "online") {
        $bFoundGroup = true;
    }
}
if (!$bFoundGroup) {
    header("HTTP/1.0 404 Not Found");
} else {
    $aDataScript["group"] = $Group;
    $aDataScript["amountMax"]= $Group->getAmount_target()-$Group->getAmount_plegde()-$Group->getAmount_income();
    $aDataScript["subkey"] = sha1(substr($Group->getKey_edit(),0,10).$Group->getId());
}

