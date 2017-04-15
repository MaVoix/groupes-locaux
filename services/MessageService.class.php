<?php
/*
 * Example twig
    1.{{ MessageService.getNode('system', 'entry', 'all-fields-required').text }}
    2.{{ MessageService.getNode('dossier', 'limit', 'contest', ConfigService.get("nb-dossiers-max")).text }}
    3.{{ MessageService.getText('dossier', 'limit', 'contest', ConfigService.get("nb-dossiers-max")) }}
    4.{{ MessageService.getNumber('dossier', 'limit', 'contest') }}

 * Example php
    1.$aDataScript['test'] = MessageService::getNode('test-lvl','level1','php poppy')->text;

 */
class MessageService
{
    const MESSAGE_FILE_PATH   = '../message.json';

    protected static function getJson(){
        $sContent = @file_get_contents(self::MESSAGE_FILE_PATH);
        $oJson    = json_decode($sContent);
        return $oJson;
    }

    public static function getNode($params){
        $oJson = self::getJson();
        if(isset($params) && is_array($params)){
            $args = $params;
            $nb_args = count($params);
        }else{
            $args = func_get_args();
            $nb_args = func_num_args();
        }

        $oReturn = null; $n = 0;
        if(isset($args[0]) && isset($oJson->$args[0])) {$n++;
            if(isset($args[1]) && isset($oJson->$args[0]->$args[1])){ $n++;
                if(isset($args[2]) && isset($oJson->$args[0]->$args[1]->$args[2])){ $n++;
                    $oReturn = $oJson->$args[0]->$args[1]->$args[2];
                }else{
                    $oReturn = $oJson->$args[0]->$args[1];}
            }else{
                $oReturn = $oJson->$args[0];}
        }

        if($n<$nb_args && isset($oReturn) && isset($oReturn->text)){
            $rearrangeArgs = $args;
            for($i=0;$i<$n;$i++){array_shift($rearrangeArgs);}
            $str = vsprintf($oReturn->text, $rearrangeArgs);
            $oReturn->text = $str;
        }
        
        return $oReturn;
    }

    public static function getText(){
        $default = (ConfigService::get("environment")=='dev'|| ConfigService::get("environment")=='local')?'void-text':'';
        $params = func_get_args();
        $oMsg = self::getNode($params);
        $sReturn = isset($oMsg->text)?$oMsg->text:$default;
        return $sReturn;
    }

    public static function getNumber(){
        $default = (ConfigService::get("environment")=='dev'|| ConfigService::get("environment")=='local')?-1:0;
        $params = func_get_args();
        $oMsg = self::getNode($params);
        $sReturn = isset($oMsg->number)?$oMsg->number:$default;
        return $sReturn;
    }

    public static function getTitle(){
        $default = (ConfigService::get("environment")=='dev'|| ConfigService::get("environment")=='local')?'void-title':'';
        $params = func_get_args();
        $oMsg = self::getNode($params);
        $sReturn = isset($oMsg->title)?$oMsg->title:$default;
        return $sReturn;
    }

    public static function getBtcancel(){
        $default = (ConfigService::get("environment")=='dev'|| ConfigService::get("environment")=='local')?'void-btcancel':'';
        $params = func_get_args();
        $oMsg = self::getNode($params);
        $sReturn = isset($oMsg->title)?$oMsg->btcancel:$default;
        return $sReturn;
    }

    public static function getBtconfirm(){
        $default = (ConfigService::get("environment")=='dev'|| ConfigService::get("environment")=='local')?'void-btconfirm':'';
        $params = func_get_args();
        $oMsg = self::getNode($params);
        $sReturn = isset($oMsg->title)?$oMsg->btconfirm:$default;
        return $sReturn;
    }


    public static function getType(){
        $default = (ConfigService::get("environment")=='dev'|| ConfigService::get("environment")=='local')?'void-type':'';
        $params = func_get_args();
        $oMsg = self::getNode($params);
        $sReturn = isset($oMsg->type)?$oMsg->type:$default;
        return $sReturn;
    }

    public static function getListToArray(){
        $sContent   = @file_get_contents(self::MESSAGE_FILE_PATH);
        $aJson      = json_decode($sContent, true);
        $aFinalKeys = ["number", "type", "title", "text"];
        $aReturn    = [];
        $aNumbers   = [];
        $nMaxNumber = 0;

        foreach($aJson as $sKey=>$oJsonNode){
            foreach($oJsonNode as $sKeyLvl_1=>$oJsonNodeLvl1){
                if(in_array($sKeyLvl_1, $aFinalKeys)){
                    if(!isset($aReturn[$sKey])) {
                        $aReturn[$sKey] = self::shapeNodeArray($sKey, $oJsonNode);
                        if(isset($oJsonNode['number'])) {
                            if (!isset($aNumbers[$oJsonNode['number']])) {$aNumbers[$oJsonNode['number']] = [];}
                            $aNumbers[$oJsonNode['number']][]=$sKey;
                            if ($nMaxNumber < $oJsonNode['number']) {$nMaxNumber = $oJsonNode['number'];}
                        }
                    }
                }else{
                    foreach($oJsonNodeLvl1 as $sKeyLvl_2=>$oJsonNodeLvl2){
                        if(in_array($sKeyLvl_2, $aFinalKeys)){
                            if(!isset($aReturn[$sKey.':'.$sKeyLvl_1])) {
                                $aReturn[$sKey.':'.$sKeyLvl_1] = self::shapeNodeArray($sKey.':'.$sKeyLvl_1, $oJsonNodeLvl1);
                                if(isset($oJsonNodeLvl1['number'])) {
                                    if (!isset($aNumbers[$oJsonNodeLvl1['number']])) {$aNumbers[$oJsonNodeLvl1['number']] = [];}
                                    $aNumbers[$oJsonNodeLvl1['number']][]=$sKey.':'.$sKeyLvl_1;
                                    if ($nMaxNumber < $oJsonNodeLvl1['number']) {$nMaxNumber = $oJsonNodeLvl1['number'];}
                                }
                            }
                        }else{
                            if(!isset($aReturn[$sKey.':'.$sKeyLvl_1.':'.$sKeyLvl_2])) {
                                $aReturn[$sKey.':'.$sKeyLvl_1.':'.$sKeyLvl_2] = self::shapeNodeArray($sKey.':'.$sKeyLvl_1.':'.$sKeyLvl_2, $oJsonNodeLvl2);

                                if(isset($oJsonNodeLvl2['number'])) {
                                    if (!isset($aNumbers[$oJsonNodeLvl2['number']])) {$aNumbers[$oJsonNodeLvl2['number']] = [];}
                                    $aNumbers[$oJsonNodeLvl2['number']][]=$sKey.':'.$sKeyLvl_1.':'.$sKeyLvl_2;
                                    if ($nMaxNumber < $oJsonNodeLvl2['number']) {$nMaxNumber = $oJsonNodeLvl2['number'];}
                                }
                            }
                        }
                    }
                }
            }
        }

        return [
            'list'      => $aReturn,
            'numbers'   => $aNumbers,
            'max'       => $nMaxNumber
        ];
    }

    protected static function shapeNodeArray($sKey, $oJson){
        $aNode = [
            'key'=>$sKey,
            'number'    => isset($oJson['number'])?$oJson['number']:'',
            'type'      => isset($oJson['type'])?$oJson['type']:'',
            'title'     => isset($oJson['title'])?$oJson['title']:'',
            'text'      => isset($oJson['text'])?$oJson['text']:'',
            'btconfirm' => isset($oJson['btconfirm'])?$oJson['btconfirm']:'',
            'btcancel'  => isset($oJson['btcancel'])?$oJson['btcancel']:''
        ];
        return $aNode;
    }
}

