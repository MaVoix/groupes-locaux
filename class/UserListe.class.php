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
        "people_id",
        "login",
        "pass",
        "type",
        "enable"
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
    private function withEmail($sEmail)
    {
        $this-> setAllFields();
        $this->addCritere(array("field"=>"email", "value"=>strtolower(Vars::secureInjection($sEmail)), "compare"=>"="));
        return $this;
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
    }
    public function applyRules4SearchByEmail($email)
    {
        return  $this->notDeleted()->withEmail($email);
    }

}