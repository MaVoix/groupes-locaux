<?php

/**
 * Class TransactionListe
 */
class TransactionListe extends Liste
{

    /**
     * Champs de la table
     */
    private static $_champs = array(
        "id",
        "date_created",
        "date_amended",
        "date_deleted",
        "reference",
        "group_id",
        "user_id",
        "amount",
        "pledge_id",
        "path_file",
        "income",
        "expense",
        "comment",
        "payment_type",
        "date_payment",
        "date_collection"
    );

    /**
     * Constructeur
     * @param array $aParam tableau de parametres
     */
    public function __construct( $aParam=array() )
    {
        parent::__construct();
        $this->setTablePrincipale("transaction");
        $this->setAliasPrincipal("Transaction");
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


    public function applyRules4Group($group_id){
        $this->setAllFields();
        $this->addCriteres([
            [
                "field" => "date_deleted",
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
}