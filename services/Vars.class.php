<?php

/**
 * Created by Agence K-Mikaze.
 * User: Clement
 * Date: 18/01/2017
 * Time: 16:00
 */
class Vars
{
    /**
     *
     *
     * @param integer $length
     * @param array $exclude
     * @return string
     */
    public static function getRandomSha1($length, $exclude=[])
    {
        $length = intval($length);
        if( $length==0 )
        {
            throw new InvalidArgumentException( sprintf("%s::%s(%d) : Length must be greater than 0", __CLASS__, __FUNCTION__) );
        }

        $randomSha1 = function() use ($length)
        {
            $max = ceil($length / 40);
            $random = '';
            for ($i = 0; $i < $max; $i ++)
            {
                $random .= sha1(microtime(true).mt_rand(10000,90000));
            }
            return substr($random, 0, $length);
        };

        $r = $randomSha1();

        if( in_array($r, $exclude) )
        {
            $unused = false;
            $max_retry = 1000;

            while(!$unused)
            {
                $r = $randomSha1();

                if( !in_array($r, $exclude) )
                {
                    $unused = true;
                }
                elseif( $max_retry<=0 )
                {
                    $unused = true;
                    Mail::sendMail(ConfigService::get("bdd-destinataires"),
                        "Erreur rare dans une méthode de classe",
                        sprintf("%s::%s(%d) : N'a pu générer une clé non utilisée dans le tableau passé après 1 000 tentatives. La clef non unique '%s' a été retournée.", __CLASS__, __FUNCTION__, $length)
                    );
                }
                else
                {
                    $max_retry--;
                }
            }
        }

        return $r;
    }

    /**
     * Retire l'extension d'un nom de fichier
     *
     * @param $filename
     * @return string
     */
    public static function getExtension($filename)
    {
        return substr($filename, 0, strrpos($filename, '.'));
    }

    /**
     * Ajoute la valeur au tableau si celle-ci n'est pas déjà dedans
     *
     * @param $array
     * @param $value
     */
    public static function pushIfNotInArray(&$array, $value)
    {
        if( !in_array($value, $array) )
        {
            $array[] = $value;
        }
    }

    /**
     * Ajoute la paire clé/valeur au tableau si le tableau ne contient pas déjà une clé identique
     *
     * @param $array
     * @param $key
     * @param $value
     */
    public static function pushIfKeyNotExists(&$array, $key, $value)
    {
        if( !array_key_exists($key, $array) )
        {
            $array[ $key ] = $value;
        }
    }

    /**
     * @param $tel
     * @return string
     */
    public static function formatTel( $tel )
    {
        return implode(".", str_split($tel, 2));
    }

    public static function formatMontant( $montant )
    {
        $formatted_montant = "";



        $formatted_montant .= " €";

        return $formatted_montant;
    }

    /**
     * Generates a user-friendly alphanumeric random string of given length
     *
     * @param integer $length (Optional)
     *
     * @return string
     */
    public static function GetRandString($length = 8)
    {
        self::Set( $rand_str, self::GetRandomUniqueInt() );

        // Remove Z and Y from the base_convert(), replace 0 with Z and O with Y
        // [a, z] + [0, 9] - {z, y} = [a, z] + [0, 9] - {0, o} = 34
        $rand_str = str_replace(array('0', 'O'), array('Z', 'Y'), strtoupper(base_convert($rand_str, 16, 34)));

        return substr($rand_str, 0, $length);
    }



    /**
     * Return unique id
     * @return integer
     */
    public static function GetRandomUniqueInt()
    {
        return substr(md5(rand().microtime()), 4, 16);
    }

    /**
     * Définit une variable typée
     * @param mixed &$variable Référence à la variable
     * @param mixed $value Valeur à attribuer à la variable
     * @return mixed
     */
    public static function Set( &$variable, $value )
    {
        $variable = $value;

        if( is_string( $variable ) )
        {
            // no multibyte, allow only ASCII (0-127)
            //$variable = preg_replace('/[\x80-\xFF]/', '?', $variable);
        }

        self::Type( $variable );

    }

    /**
     * Type une variable existante
     * @param mixed &$variable Référence à la variable
     */
    public static function Type( &$variable )
    {
        settype( $variable, self::GetType( $variable ) );
    }

    /**
     * Retourne le type de la variable passée
     * @param mixed $value Variable à tester
     * @return string
     */
    public static function GetType( $value )
    {
        switch(true)
        {
            case ( is_null( $value ) ):
                return "NULL";
                break;

            case ( is_bool( $value ) ):
                return "boolean";
                break;

            case ( is_integer( $value ) ):
                return "integer";
                break;

            case ( is_float( $value ) ):
                return "float";
                break;

            case ( is_double( $value ) ):
                return "double";
                break;

            case ( is_array( $value ) ):
                return "array";
                break;

            case ( is_object( $value ) ):
                return "object";
                break;

            case ( is_string( $value ) ):
            default:
                return "string";
                break;
        }
    }

    /**
     * Retire les accents d'une chaine de caractère
     * @param string &$str Chaine à traiter
     * @param string $charset Encodage
     * @return string
     */
    public static function RemoveAccents(&$str, $charset='utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
    }



    /**
     * Retourne le mois textuel à partir de son numéro, dans la langue désirée
     *
     * @param integer $month_number Numéro du mois
     * @param string $language (Optional) Langue, défaut anglais
     *
     * @return null
     */
    public static function getTextMonth($month_number, $language="en")
    {
        $months = array(
            "fr" => array(
                "",
                "Janvier",
                "Février",
                "Mars",
                "Avril",
                "Mai",
                "Juin",
                "Juillet",
                "Août",
                "Septembre",
                "Octobre",
                "Novembre",
                "Décembre"
            ),
            "en" => array(
                "",
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
            )
        );

        $language = trim(strtolower($language));
        if( array_key_exists($language, $months))
        {
            $month_number = intval( $month_number );
            if( array_key_exists($month_number, $months[ $language ]) )
            {
                return $months[ $language ][ $month_number ];
            }
            else return null;
        }

        return null;
    }

    /**
     * fonction qui renvoi le jour de la semaine par rapport a une date
     * @param $sDate
     * @param string $language
     * @return mixed
     */
    public static function getDayFromDate($sDate, $language="en"){
        $nNumJ = date("N",strtotime($sDate));
        $aTransalte = array(
            'fr'=>array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'),
            'en'=>array('Monday','Tuesday','WednesDay','Thursday','Friday','Saturday','Sunday')
        );
        switch($language ){
            case "fr":break;
            default:$language = 'en';break;
        }
        $sDay = $aTransalte[$language][$nNumJ-1];
        return $sDay;
    }

    /**
     * fonction qui renvoi un nombre sous forme de chaine de caractère
     * @param int $nDouble
     * @param string $sLangue langue du navigateur
     * @param bool $bShowMonnaie affiche ou masque le symbole de la monnaie
     * @param int $nNbDecimal nombre de chiffres apres la virgule
     * @return string formatée
     */
    public static function formatPrix($nDouble=0, $sLangue="en",$bShowMonnaie=true,$nNbDecimal=2){
        //$sStr=(string)$nDouble;
        //$sReturn="";
        if($sLangue=="fr"){
            $sReturn=number_format($nDouble, $nNbDecimal, ',', ' ');
        }else{
            $sReturn=number_format($nDouble, $nNbDecimal, '.', ',');
        }
        if($bShowMonnaie){
            return $sReturn." ".ConfigService::get("sigle-monnaie");
        }else{
            return $sReturn;
        }

    }
    public static function formatPrixPDF($nDouble=0, $sLangue="en",$bShowMonnaie=true,$nNbDecimal=2){
        return str_replace(ConfigService::get("sigle-monnaie"),chr(128),self::formatPrix($nDouble, $sLangue,$bShowMonnaie,$nNbDecimal));
    }

    /**
     * verifie qu'une adresse e-mail est valide
     * @param $sEmail
     * @return bool
     */
    public static function checkMail($sEmail){
        $sPattern='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
        if(!preg_match($sPattern,$sEmail)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * verifie qu'un siret est valide
     * @param $siret
     * @return bool
     */
    public static function checkSiret($siret){
        if($siret=="testsiret"){return true;}
        // suppression des espaces en trop
        $siret = str_replace(' ', '', $siret);
        if(!preg_match("/^(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)$/", $siret,$match)){
            return false;
        }else{
            //$retour_siren = check_siren(implode('', array_slice($match, 1,9)));
            //if (is_string($retour_siren)){
            //	return false;
            //}else{
            $match[1] *= 2;
            $match[3] *= 2;
            $match[5] *= 2;
            $match[7] *= 2;
            $match[9] *= 2;
            $match[11] *= 2;
            $match[13] *= 2;

            $somme = 0;

            for ($i = 1; $i<count($match); $i++)
            {if ($match[$i] > 9)
            {
                $a = (int)substr($match[$i], 0, 1);
                $b = (int)substr($match[$i], 1, 1);
                $match[$i] = $a + $b;
            }
                $somme += $match[$i];
            }

            if (($somme % 10) == 0){ return true;
            }else{
                return false;
            }
        }
    }


    public static function checkNumeroTel($sTel){
        $tel = preg_replace("[^0-9]","",$sTel);
        $motif ='`^0[1-9][0-9]{8}$`';
        if(!preg_match($motif,$tel))
        {
            return false;
        }else{
            return true;
        }
    }


    public static function getPluriel($sMot,$nQte){
        $sReturn=$sMot;
        if($nQte>=2){
            if(substr($sMot,-2)=="al"){

            }else{
                $sReturn=$sMot."s";
            }
        }else{

        }
        return $sReturn;
    }


    public static function formatStringForIphone($sTexte){
        $sTexte=preg_replace('/((\d)|([a-z<>]+))/', '&#8203;${1}', $sTexte);
        return $sTexte;

    }

    public static function protectCSV($sChaine,$nTaille=0){


        $sChaine=str_replace(";",",",$sChaine);
        $sChaine=str_replace("\n",' ',$sChaine);
        $sChaine=str_replace("\r",' ',$sChaine);
        $sChaine=utf8_decode($sChaine);
        $sChaine= str_pad($sChaine,$nTaille, "0", STR_PAD_LEFT);
        if($nTaille>0){
            $sChaine=substr($sChaine,0,$nTaille);
        }

        return $sChaine;
    }

    public static function cesure( $text,$nTailleLigne){
        if($text){
            $newtext = wordwrap( $text,$nTailleLigne,"\n",1);
            $aTextChaines= explode("\n",$newtext );
            $n=0;
            foreach($aTextChaines as $sTextChaine){
                $sTextChaine=str_pad($sTextChaine,$nTailleLigne, " ", STR_PAD_RIGHT);
                $aTextChaines[$n]=$sTextChaine;
                $n++;
            }
            return $aTextChaines;
        }else{
            $aArray=array();
            return $aArray;
        }
    }


    //secureInjection
    public static function secureInjection($sChaine){
        $sChaine=stripslashes($sChaine);
        return addslashes($sChaine);
    }




}

