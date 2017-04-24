<?php
/**
 * Class UserListe
 */
class UserListe extends Liste
{

    /**
     * Champs de la table
     */
    private static $_champs = array(
        "id",
        "date_created",
        "date_amended",
        "date_deleted",
        "email",
        "pass",
        "type",
        "enable",
        "group_id",
        "civility",
        "firstname",
        "name",
        "ad1",
        "ad2",
        "ad3",
        "city",
        "zipcode",
        "country",
        "tel"
    );

    /**
     * Constructeur
     * @param array $aParam tableau de parametres
     */
    public function __construct( $aParam=array() )
    {
        parent::__construct();
        $this->setTablePrincipale("user");
        $this->setAliasPrincipal("User");
        /*$this->setTri("");
        $this->setSens("DESC");
        $this->setSearchFields(array(
        array("field"=>"nom")
        ))*/
    }

    /**
     * Access champs table
     */
    public static function champs()
    {
        return self::$_champs;
    }

    /**
     * Methode pour récupérer tous les champs
     */
    public function setAllFields()
    {
        $this->setFields(self::$_champs);
    }

    private function notDeleted()
    {
        $this->setAllFields();
        $this->addCriteres([
            [
                "field" => "date_deleted",
                "compare" => "IS NULL",
                "value" => ""
            ]
        ]);
        return $this;
    }
    private function withEmail($sEmail,$sType)
    {
        $this-> setAllFields();
        $this->addCritere(array("field"=>"email", "value"=>strtolower(Vars::secureInjection($sEmail)), "compare"=>"="));
        if(!is_null($sType)){
            $this->addCritere(array("field"=>"type", "value"=>strtolower(Vars::secureInjection($sType)), "compare"=>"="));
        }
        return $this;
    }
    public function applyRules4Group($id,$type=null)
    {
        $this->setAllFields();
        $this->addCriteres([
            [
                "field" => "group_id",
                "compare" => "=",
                "value" => vars::secureInjection(intval($id))
            ]
        ]);
        if(!is_null($type)){
            $this->addCriteres([
                [
                    "field" => "type",
                    "compare" => "=",
                    "value" => vars::secureInjection($type)
                ]
            ]);
        }
        $this->notDeleted();
    }
    public function applyRules4SearchByEmail($email,$sType=null)
    {
        return  $this->notDeleted()->withEmail($email,$sType);
    }


    public function applyRules4Connexion($email, $pass)
    {
        $this->setAllFields();
        $this->notDeleted();

        $this->addCriteres([
            [
                "field" => "email",
                "compare" => "=",
                "value" => vars::secureInjection($email)
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "pass",
                "compare" => "=",
                "value" => vars::secureInjection($pass)
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "pass",
                "compare" => "!=",
                "value" => ""
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "enable",
                "compare" => "=",
                "value" => "1"
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "type",
                "compare" => "!=",
                "value" => "membre"
            ]
        ]);

        return $this;
    }



}