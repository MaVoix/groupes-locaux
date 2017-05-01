<?php

/**
 * Class PledgeListe
 */
class PledgeListe extends Liste
{

    /**
     * Champs de la table
     */
    private static $_champs = array(
        "id",
        "date_created",
        "date_amended",
        "date_deleted",
        "date_completed",
        "civility",
        "name",
        "firstname",
        "zipcode",
        "email",
        "tel",
        "amount",
        "key_edit",
        "group_id",
        "reference"
    );

    /**
     * Constructeur
     * @param array $aParam tableau de parametres
     */
    public function __construct( $aParam=array() )
    {
        parent::__construct();
        $this->setTablePrincipale("pledge");
        $this->setAliasPrincipal("Pledge");
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

    public function applyRules4GroupCurrent($group_id){
        $this->setAllFields();
        $this->addCriteres([
            [
                "field" => "date_deleted",
                "compare" => "IS NULL",
                "value" => ""
            ],
            [
                "field" => "date_completed",
                "compare" => "IS NULL",
                "value" => ""
            ]
        ]);
        $this->addCriteres([
            [
                "field" => "group_id",
                "compare" => "=",
                "value" => intval($group_id)
            ]
        ]);
        $this->setTri("date_created");
        $this->setSens("ASC");
    }

    public function applyRules4Reference($reference, $group_id){
        $this->setAllFields();
        $this->addCriteres([
            [
                "field" => "reference",
                "compare" => "=",
                "value" => vars::secureInjection(trim($reference))
            ]
        ]);
        $this->addCriteres([
            [
                "field" => "group_id",
                "compare" => "=",
                "value" => intval($group_id)
            ]
        ]);
    }

}
