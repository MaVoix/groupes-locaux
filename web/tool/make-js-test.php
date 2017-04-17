<?php
$time_start = microtime(true);

function debug($msg){
    global $time_start;
    $time_end = microtime(true);
    $time = $time_end - $time_start;
    echo "\n".round($time,4)." : ".$msg." -- ";
}

debug("include maintenance");
include "../../maintenance.php";
debug("ini set");
ini_set("memory_limit","1024M");
ini_set("max_execution_time",30);
$pathRoot = '../../';
$pathJS = '../js/'; //with slash at the end
$pathOutJSMin = '../js/site.min.js';
$addedFilesAtFirst = array($pathRoot . "vendor/components/jquery/jquery.js", $pathRoot . "vendor/twitter/bootstrap/dist/js/bootstrap.js");
$addedFilesAtEnd = array($pathJS . "bind.js");
$excludedFiles=array($pathOutJSMin);
$allFiles = array();
$out = "";
if( in_array($_SERVER["REMOTE_ADDR"],$aIp)){
    //composer loader
    debug("include composer");
    require_once '../../vendor/autoload.php';
    debug("Recursive directory");
    $directory = new RecursiveDirectoryIterator($pathJS);
    debug("Recursive iterator");
    $iterator = new RecursiveIteratorIterator($directory);
    $files = new RegexIterator($iterator, '/^.+\.js$/i', RecursiveRegexIterator::GET_MATCH);
    debug("foreach files addedfirst");
    foreach ($addedFilesAtFirst as $file) {
        array_push($allFiles, $file);
    }
    debug("foreach files iterator");
    foreach ($files as $file) {
        $sPathFile=str_replace("\\","/",$file[0]);
        if (!in_array($sPathFile, $addedFilesAtFirst) && !in_array($sPathFile, $addedFilesAtEnd)  && !in_array($sPathFile,$excludedFiles) ) {
            array_push($allFiles, $sPathFile);
        }
    }
    debug("foreach files addedAtEnd");
    foreach ($addedFilesAtEnd as $file) {
        array_push($allFiles, $file);
    }
  /*  $out="";
    debug("foreach reading");
    foreach ($allFiles as $file) {
        $out.="\n\n".file_get_contents($file);
    }
    debug("ile put content");
    file_put_contents($pathOutJSMin, $out);*/
    debug("instance minify");
    $minifier = new MatthiasMullie\Minify\JS();
    debug("add files to minify");
    foreach ($allFiles as $file) {
        $minifier->add($file) ;
    }
    debug("minify");
    $minifier->minify($pathOutJSMin);
    debug("end");
    echo 1;
}else{
    echo 0;
}
