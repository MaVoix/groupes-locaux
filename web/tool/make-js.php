<?php
include "../../maintenance.php";
ini_set("memory_limit","1024M");
ini_set("max_execution_time",30);
$pathRoot = '../../';
$pathJS = '../js/'; //with slash at the end
$pathOutJSMin = '../js/site.min.js';
$addedFilesAtFirst = array($pathRoot . "vendor/components/jquery/jquery.js", $pathRoot . "vendor/twitter/bootstrap/dist/js/bootstrap.js");
$addedFilesAtEnd = array($pathJS . "bind.js");
$excludedFiles=array($pathOutJSMin,"../js/plugins/intlTelInput/utils.js");
$allFiles = array();
$out = "";
if( in_array($_SERVER["REMOTE_ADDR"],$aIp)){
    //composer loader
    require_once '../../vendor/autoload.php';
    $directory = new RecursiveDirectoryIterator($pathJS);
    $iterator = new RecursiveIteratorIterator($directory);
    $files = new RegexIterator($iterator, '/^.+\.js$/i', RecursiveRegexIterator::GET_MATCH);
    foreach ($addedFilesAtFirst as $file) {
        array_push($allFiles, $file);
    }
    foreach ($files as $file) {
        $sPathFile=str_replace("\\","/",$file[0]);

        if (!in_array($sPathFile, $addedFilesAtFirst) && !in_array($sPathFile, $addedFilesAtEnd)  && !in_array($sPathFile,$excludedFiles) ) {
            array_push($allFiles, $sPathFile);
        }
    }
    foreach ($addedFilesAtEnd as $file) {
        array_push($allFiles, $file);
    }
    $minifier = new MatthiasMullie\Minify\JS();
    foreach ($allFiles as $file) {
        $minifier->add($file) ;
    }
    $minifier->minify($pathOutJSMin);
    echo 1;
}else{
    echo 0;
}
