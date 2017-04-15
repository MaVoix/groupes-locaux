<?php

include "../../maintenance.php";
ini_set("memory_limit","1024M");
ini_set("max_execution_time",30);
$pathRoot = '../../';
$pathCSS = '../css/'; //with slash at the end
$pathOutCSSMin = '../css/site.min.css';
$addedFilesAtFirst = array($pathRoot . "vendor/twitter/bootstrap/dist/css/bootstrap.css", $pathRoot . "vendor/components/font-awesome/css/font-awesome.css");
$pathVendorReplace=array("../../vendor/components/font-awesome/"=>"");
$addedFilesAtEnd = array($pathCSS . "styles.css");
$excludedFiles=array(basename($pathOutCSSMin));
$allFiles = array();
$out = "";
if( in_array($_SERVER["REMOTE_ADDR"],$aIp)){
    //composer loader
    require_once '../../vendor/autoload.php';
    $directory = new RecursiveDirectoryIterator($pathCSS);
    $iterator = new RecursiveIteratorIterator($directory);
    $files = new RegexIterator($iterator, '/^.+\.css$/i', RecursiveRegexIterator::GET_MATCH);
    foreach ($addedFilesAtFirst as $file) {
        array_push($allFiles, $file);
    }
    foreach ($files as $file) {
        if (!in_array($file[0], $addedFilesAtFirst) && !in_array($file[0], $addedFilesAtEnd)  && !in_array(basename($file[0]),$excludedFiles) ) {
            array_push($allFiles, $file[0]);
        }
    }
    foreach ($addedFilesAtEnd as $file) {
        array_push($allFiles, $file);
    }
    $minifier = new MatthiasMullie\Minify\CSS();
    foreach ($allFiles as $file) {
        $minifier->add($file) ;
    }
    $minifier->minify($pathOutCSSMin);
    $sCSS=file_get_contents($pathOutCSSMin);
    //replace vendor path ...
    foreach($pathVendorReplace as $sPath=>$sValue){
        $sCSS=str_replace($sPath,$sValue,$sCSS);
    }
    file_put_contents($pathOutCSSMin,$sCSS);
    echo 1;
}else{
    echo 0;
}