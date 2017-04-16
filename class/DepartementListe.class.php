<?php

/**
 * Class DepartementListe
 */
class DepartementListe extends Liste
{

    /**
     * Champs de la table
     */
    private static $_champs = array(
        "id",
        "date_created",
        "date_amended",
        "date_deleted",
        "code",
        "name",
        "name_uppercase",
        "slug",
        "name_soundex"
    );

    /**
     * Constructeur
     * @param array $aParam tableau de parametres
     */
    public function __construct( $aParam=array() )
    {
        parent::__construct();
        $this->setTablePrincipale("departement");
        $this->setAliasPrincipal("Departement");
        $this->setSens("ASC");
        $this->setTri("code");
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

    public function applyRules4All()
    {
        $this->setFields(self::$_champs);

        $this->notDeleted();
    }



}
