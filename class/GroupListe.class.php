<?php

/**
 * Class GroupListe
 */
class GroupListe extends Liste
{

    /**
     * Champs de la table
     */
    private static $_champs = array(
        "id",
        "name",
        "date_created",
        "date_amended",
        "date_deleted",
        "state",
        "departement",
        "circonscription",
        "path_pic",
        "bank_name",
        "bank_city",
        "amount_promises",
        "amount_donations",
        "key_edit"
    );

    /**
     * Constructeur
     * @param array $aParam tableau de parametres
     */
    public function __construct( $aParam=array() )
    {
        parent::__construct();
        $this->setTablePrincipale("group");
        $this->setAliasPrincipal("Group");
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

    public function applyRules4Key($key,$id)
    {
        $this->setAllFields();

        $this->addCriteres([
            [
                "field" => "key_edit",
                "compare" => "=",
                "value" => vars::secureInjection($key)
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "id",
                "compare" => "=",
                "value" => vars::secureInjection($id)
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "date_deleted",
                "compare" => "IS NULL",
                "value" => ""
            ]
        ]);




        return $this;
    }
}
