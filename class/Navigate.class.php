<?php

class Navigate
{
    private $user=null;
    private $page=null;
    private $area=null;
    private $format=null;
    private $type=null;
    private $template=null;
    private $data_template=array();

    public function __construct(User $oUser)
    {
        $this->user = $oUser;

        if( !$oUser->getType() )
        {
            $this->type = "visitor";
        }
        else
        {
            $this->type = $oUser->getType();
        }

        if(!isset($_GET["page"]))
        {
            $_GET["page"] = $this->getDefaultPage();
        }

        if(!isset($_GET["area"]))
        {
            $_GET["area"] = $this->getDefaultArea();
        }

        if(!isset($_GET["format"]))
        {
            $_GET["format"] = $this->getDefaultPageFormat();
        }

        $this->page   = $_GET["page"];
        $this->area   = $_GET["area"];
        $this->format = $_GET["format"];
    }

    public function setUser(User $User)
    {
        $this->user = $User;
        return $this;
    }

    public function getDefaultPageFormat()
    {
        return ConfigService::get("format-default");
    }

    public function getDefaultPage()
    {
        $default_pages = ConfigService::get("page-default");
        $user_type = $this->getUser()->getType();

        if( array_key_exists($user_type, $default_pages) )
        {
            return $default_pages[ $user_type ];
        }

       else return "@no-default-page";
    }

    public function getDefaultArea()
    {
        $default_areas = ConfigService::get("area-default");
        $user_type = $this->getUser()->getType();

        if( array_key_exists($user_type, $default_areas) )
        {
            return $default_areas[ $user_type ];
        }

        else return "@no-default-area";
    }

    public function getDefaultPath()
    {
        return sprintf("/%s/%s.%s", $this->getDefaultArea(), $this->getDefaultPage(), $this->getDefaultPageFormat());
    }

    public function loadPage($sPageDir,$sTemplateDir){

        $sPathOfScript=$sPageDir."/".$this->type."/".$this->area."/".$this->page.".php";
        if(!file_exists($sPathOfScript)){
            $aTypes=explode("|",ConfigService::get("types"));
            foreach($aTypes as $sTypes){
                $aType=explode(">",$sTypes);
                if($aType[0]==$this->type){
                    foreach($aType as $sType){
                        if(!file_exists($sPathOfScript)){
                            $sPathOfScript=$sPageDir."/".$sType."/".$this->area."/".$this->page.".php";

                        }
                    }
                }
            }
        }
        $sPathOfTemplate="";
        if( $this->format == "html")
        {
            $sPathOfTemplate=$sTemplateDir."/".$this->type."/".$this->area."/".$this->page.".html.twig";

            if(!file_exists( $sPathOfTemplate)){
                $aTypes=explode("|",ConfigService::get("types"));
                foreach($aTypes as $sTypes){
                    $aType=explode(">",$sTypes);
                    if($aType[0]==$this->type){
                        foreach($aType as $sType){
                            if(!file_exists( $sPathOfTemplate)){
                                $sPathOfTemplate=$sTemplateDir."/".$sType."/".$this->area."/".$this->page.".html.twig";
                            }
                        }
                    }
                }
            }
        }

        if( $this->format == "xml")
        {
            header("Content-type: text/xml");
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            $sPathOfTemplate = $sTemplateDir."/xml.twig";
        }

        if( $this->format == "json")
        {
            header("Content-type: application/json");
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            $sPathOfTemplate = $sTemplateDir."/json.twig";
        }

        if( $this->format == "png")
        {
           // header("Content-type: image/png");
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            $sPathOfTemplate = $sTemplateDir."/png.twig";
        }

        $aDataScript = [];
        $oMe = $this->user;
        $oNavigate = $this;

        if(file_exists($sPathOfScript))
        {
            require_once $sPathOfScript;
        }

        //compilation JS
        if(ConfigService::get("js_auto_reload")==true){
            $retour=file_get_contents(ConfigService::get("urlSite")."/tool/make-js.php");
            if($retour==0){
                echo "Impossible de compiler le fichier JS.(vérifier la liste des IP dans maintenance.php)";
            }
        }
        $aDataScript["versionjs"]=filemtime('js/site.min.js');

        //compilation CSS
        if(ConfigService::get("css_auto_reload")==true){
            $retour=file_get_contents(ConfigService::get("urlSite")."/tool/make-css.php");
            if($retour==0){
                echo "Impossible de compiler le fichier CSS.(vérifier la liste des IP dans maintenance.php)";
            }
        }
        $aDataScript["versioncss"]=filemtime('css/site.min.css');

        $this->data_template=$aDataScript;

        $this->template=substr($sPathOfTemplate,strlen($sTemplateDir."/"));

        $this->historyTrack();

    }

    public function getUser(){
        return $this->user;
    }

    public function getPage(){
        return $this->page;
    }

    public function getArea(){
        return $this->area;
    }

    public function getType(){
        return $this->type;
    }

    public function getKeyclt(){
        return $this->keyclt;
    }

    public function getFormat(){
        return $this->format;
    }

    public function getTemplate(){
        return $this->template;
    }

    public function getDataTemplate(){
        return $this->data_template;
    }

    public function getHistory()
    {
        $history = SessionService::get("history");

        if( is_null($history) )
        {
            $history = [];
            SessionService::set("history", $history);
        }

        return $history;
    }

/*    public function getPath()
    {
        return sprintf("%s/%s/%s.%s", ConfigService::get("urlSite"), $this->getArea(), $this->getPage(), $this->getFormat());
    }*/

    public function removeHistoryPreviousUrl($howMany=1)
    {
        $howMany++;
        $history = $this->getHistory();

        if( count($history)>1 )
        {
            $n = count($history)-$howMany;
            if( array_key_exists($n, $history) )
            {
                unset($history[$n]);
            }
        }

        SessionService::set("history", $history);
    }

    public function getHistoryPreviousUrl($howMany=1, $andDeleteCurrent=false)
    {
        $howMany++;

        $history = $this->getHistory();
        $url = null;

        if( count($history)>1 )
        {
            $n = count($history)-$howMany;
            if( array_key_exists($n, $history) )
            {
                $url = $history[$n];

                if( $andDeleteCurrent )
                {
                    $this->addParametersToURLStr($url, [
                        "hdp" => 1
                    ]);
                }
            }
        }

        return $url;
    }

    private function addParametersToURLStr(&$url, array $newParameters)
    {
        $parsedUrl = parse_url($url);

        if (is_null(@$parsedUrl['path']))
        {
            $url .= '/';
        }

        $separator = (is_null(@$parsedUrl['query'])) ? '?' : '&';

        $query = http_build_query($newParameters);

        $url .= $separator . $query;
    }

    private function removeParametersFromURLStr(&$url, array $removeParameters)
    {
        $parsed = parse_url($url);
        $query = is_null(@$parsed['query']) ? "" : $parsed['query'];
        $path = is_null(@$parsed["path"]) ? "/" : $parsed["path"];
        $port = is_null(@$parsed["port"]) ? "" : ":{$parsed["port"]}";

        parse_str($query, $params);

        foreach( $removeParameters as $paramName )
        {
            if( array_key_exists($paramName, $params) )
            {
                unset($params[$paramName]);
            }
        }

        $separator = (count($params)>0) ? '?' : '';

        $query = http_build_query($params);
        $url = "{$parsed["scheme"]}://{$parsed["host"]}{$port}{$path}{$separator}{$query}";
    }

    public function historyTrack()
    {
        global $_GET, $_POST;

        if( !($this->getArea()==="home" AND $this->getPage()==="keep-awake") )
        {

//            if ($this->getFormat() === "html") echo '<div style="height: 150px;"></div>';

            $history = $this->getHistory();

            $uri = $_SERVER["REQUEST_URI"];
            $host = ConfigService::get("urlSite");

            if (array_key_exists("hdp", $_GET)) {
                array_pop($history);
                SessionService::set("history", $history);
            }

            if ($this->getFormat() === "html"
                AND !array_key_exists("noHistoryTrack", $_GET)
                AND !array_key_exists("noHistoryTrack", $_POST)
            ) {
                $previousUrl = $this->getHistoryPreviousUrl(0);
                $url = sprintf("%s%s", $host, $uri);
                $this->removeParametersFromURLStr($url, ["hdp"]);
                if ($previousUrl != $url) {
                    $history[] = $url;
                }
            }

//            if ($this->getFormat() === "html") var_dump($history);

            SessionService::set("history", $history);
        }
    }
}