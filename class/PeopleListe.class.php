<?php

/**
 * Class PeopleListe
 */
class PeopleListe extends Liste
{

    /**
     * Champs de la table
     */
    private static $_champs = array(
        "id",
        "date_created",
        "date_amended",
        "date_deleted",
        "group_id",
        "firstname",
        "name",
        "ad1",
        "ad2",
        "ad3",
        "city",
        "zipcode",
        "country",
        "type",
        "email",
        "tel"
    );

    /**
     * Constructeur
     * @param array $aParam tableau de parametres
     */
    public function __construct( $aParam=array() )
    {
        parent::__construct();
        $this->setTablePrincipale("people");
        $this->setAliasPrincipal("People");
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

    public function applyRules4Group($id)
    {
        $this->setAllFields();

        $this->addCriteres([
            [
                "field" => "group_id",
                "compare" => "=",
                "value" => vars::secureInjection(intval($id))
            ]
        ]);

        $this->addCriteres([
            [
                "field" => "date_deleted",
                "compare" => "IS NULL",
                "value" => ""
            ]
        ]);
    }
}
