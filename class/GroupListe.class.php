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
        "departement_id",
        "circonscription_id",
        "path_pic",
        "bank_name",
        "bank_city",
        "amount_promises",
        "amount_donations",
        "posters",
        "ballots",
        "professions_de_foi",
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

    public function applyRules4ListAdmin()
    {
        $this->setAllFields();
        $this->addCriteres([
            [
                "field" => "date_deleted",
                "compare" => "IS NULL",
                "value" => ""
            ]
        ]);

    }
}
