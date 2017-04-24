<?php

/**
 * Class CirconscriptionListe
 */
class CirconscriptionListe extends Liste
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
        "code_departement",
        "number"
    );

    /**
     * Constructeur
     * @param array $aParam tableau de parametres
     */
    public function __construct( $aParam=array() )
    {
        parent::__construct();
        $this->setTablePrincipale("circonscription");
        $this->setAliasPrincipal("Circonscription");
        $this->setTri("number");
        $this->setSens("asc");
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

    public function applyRules4Departement($nId)
    {
        $this->setFields(self::$_champs);

        $this->notDeleted();

        $this->setField([
            "table" => "departement",
            "tb_alias" => "Departement",
            "field" => "Departement.id",
            "table_principale" => $this->getTablePrincipale(),
            "fd_alias"=> "departement_id",
            "jointure" => "Departement.code={$this->getAliasPrincipal()}.code_departement AND Departement.id='".vars::secureInjection($nId)."'"
        ]);
    }
}
