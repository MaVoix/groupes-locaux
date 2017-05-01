<?php	/**
 * Class Transaction
 */
class Transaction	{

    private $aDataNonHydrate = array();
    private $aDataSet = array();
    private $callHydrateFromBDDOnGet = 0;

    private $_sDbInstance = null;

    private $nId;
    private $sDate_created;
    private $sDate_amended;
    private $sDate_deleted;
    private $sReference;
    private $nGroup_id;
    private $nUser_id;
    private $nAmount;
    private $nPledge_id;
    private $sPath_file;
    private $nIncome;
    private $nExpense;
    private $sComment;


    /**
     * Constructeur
     * @param array $aParam tableau de parametres ( clé "id" pour instancier un transaction avec un id précis )
     * @param $sDbInstance (Opt) Nom de l'instance de la bdd à utiliser
     */
    public function __construct( $aParam=array(), $sDbInstance=null )
    {
        $this->hydrate($aParam);
        $this->nId = ( isset($aParam['id']) ) ? $aParam['id'] : 0;
        $this->_sDbInstance = $sDbInstance;
    }

    /**
     * Fonction permettant d'hydrater un objet
     * @param $aDonnees array tableau clé-valeur à hydrater ( par exemple "nom"=>"DUPONT" )
     */
    public function hydrate($aDonnees)
    {
        foreach ($aDonnees as $sKey => $sValue)
        {
            if(!is_int($sKey))
            {
                $sMethode = 'set'.ucfirst($sKey);
                if (method_exists($this, $sMethode))
                {
                    if( is_null($sValue) ) $sValue="";
                    $this->$sMethode($sValue);
                }
                else
                {
                    //echo "<br />Transaction->$sMethode() n'existe pas!";
                    $this->addDataNonHydrate($sKey,$sValue);
                }
            }
        }
    }

    /**
     * Fonction permettant d'hydrater un objet à partir d'une liste de champs (s'appuie sur l'id de l'objet)
     * @param $aFields array tableau contenant la liste des champs à hydrater ( '*' pour tous)
     */
    public function hydrateFromBDD($aFields=array())
    {
        if(count($aFields))
        {
            //hydrate uniquement les champs de base (pour le reste coder directement dans les acesseurs)
            $aData=DbLink::getInstance($this->_sDbInstance)->selectForHydrate($this->getId(),"transaction",$aFields);

            //hydrate l'objet
            $this->hydrate($aData);
        }
    }


    /**
     * Fonction permettant d'ajouter des données non-hydratées à l'objet
     * @param string $sKey champs
     * @param mixed $sValue valeur
     */
    public function addDataNonHydrate($sKey,$sValue)
    {
        $this->aDataNonHydrate[$sKey]=$sValue;
    }

    /**
     * Fonction permettant de récuperer des données non-hydratées à l'objet
     * @param string $sKey champs à récupérer
     * @return string valeur du champ
     */
    public function getDataNonHydrate($sKey)
    {
        if(isset($this->aDataNonHydrate[$sKey]))
        {
            return $this->aDataNonHydrate[$sKey];
        }
        else
        {
            return "";
        }
    }

    /**
     * Fonction permettant de supprimer fictivement un objet (en lui passant un date supprime)
     */
    public function supprime()
    {
        $this->setDate_deleted(date("Y-m-d H:i:s"));
        $this->save();
    }

    /**
     * Fonction permettant de supprimer réellement un objet (en faisant un DELETE )
     */
    public function delete()
    {
        $oReq=DbLink::getInstance($this->_sDbInstance)->prepare('DELETE FROM '."transaction".' WHERE  id=:id ');
        $oReq->execute(array("id"=>$this->getId()));
        $this->vide();
    }

    /**
     * Consulte la base de données pour savoir si l'objet existe, en le recherchant par son id
     * @static
     * @param int $nId Id de l'objet à chercher
     * @param $sDbInstance (Opt) Nom de l'instance de la bdd
     * @return bool Vrai si l'objet existe, Faux sinon
     */
    public static function exists($nId=0, $sDbInstance=null)
    {
        $oReq=DbLink::getInstance($sDbInstance)->prepare('SELECT id FROM '."transaction".' WHERE  id=:id ');
        $oReq->execute(array("id"=>$nId));
        $aRes=$oReq->getRow(0);
        return (count($aRes)!=0);
    }

    /**
     * Sauvegarde l'objet en base
     */
    public function save()
    {
        $aData=array();
        if(isset($this->aDataSet["date_created"]))
        {
            $aData["date_created"]=$this->getDate_created();
        }

        if(isset($this->aDataSet["date_amended"]))
        {
            $aData["date_amended"]=$this->getDate_amended();
        }

        if(isset($this->aDataSet["date_deleted"]))
        {
            $aData["date_deleted"]=$this->getDate_deleted();
        }

        if(isset($this->aDataSet["reference"]))
        {
            $aData["reference"]=$this->getReference();
        }

        if(isset($this->aDataSet["group_id"]))
        {
            $aData["group_id"]=$this->getGroup_id();
        }

        if(isset($this->aDataSet["user_id"]))
        {
            $aData["user_id"]=$this->getUser_id();
        }

        if(isset($this->aDataSet["amount"]))
        {
            $aData["amount"]=$this->getAmount();
        }

        if(isset($this->aDataSet["pledge_id"]))
        {
            $aData["pledge_id"]=$this->getPledge_id();
        }

        if(isset($this->aDataSet["path_file"]))
        {
            $aData["path_file"]=$this->getPath_file();
        }

        if(isset($this->aDataSet["income"]))
        {
            $aData["income"]=$this->getIncome();
        }

        if(isset($this->aDataSet["expense"]))
        {
            $aData["expense"]=$this->getExpense();
        }

        if(isset($this->aDataSet["comment"]))
        {
            $aData["comment"]=$this->getComment();
        }

        if($this->getId()>0)
        {
            DbLink::getInstance($this->_sDbInstance)->update("transaction",$aData,' id="'.$this->getId().'" ');
        }
        else
        {
            $this->setId(DbLink::getInstance($this->_sDbInstance)->insert("transaction",$aData));
        }
        $this->aDataSet=array();
    }

    /**
     * Deshydrate complement l'objet, et vide la liste des champs à sauvegarder
     */
    private function vide()
    {
        $this->callHydrateFromBDDOnGet=0;
        $this->aDataSet=array();
        $this->setDate_created(NULL);
        $this->setDate_amended(NULL);
        $this->setDate_deleted(NULL);
        $this->setReference(NULL);
        $this->setGroup_id(NULL);
        $this->setUser_id(NULL);
        $this->setAmount(0);
        $this->setPledge_id(NULL);
        $this->setPath_file(NULL);
        $this->setIncome(0);
        $this->setExpense(0);
        $this->setComment(NULL);
    }

    /**
     * Renvoie l'objet sous forme de chaine de caractère
     */
    public function __toString()
    {
        $aObjet = [
            "id" => $this->getId(),
            "date_created" => $this->getDate_created(),
            "date_amended" => $this->getDate_amended(),
            "date_deleted" => $this->getDate_deleted(),
            "reference" => $this->getReference(),
            "group_id" => $this->getGroup_id(),
            "user_id" => $this->getUser_id(),
            "amount" => $this->getAmount(),
            "pledge_id" => $this->getPledge_id(),
            "path_file" => $this->getPath_file(),
            "income" => $this->getIncome(),
            "expense" => $this->getExpense(),
            "comment" => $this->getComment()
        ];

        return json_encode($aObjet);
    }








    /**
     * Set le champ id
     * @param number $nId nouvelle valeur pour le champ id
     */
    public function setId($nId)
    {
        if( is_null($nId) ) $nId='';
        if( is_numeric($nId)  || $nId=='' )
        {
            $this->nId = $nId;
            $this->aDataSet["id"]=1;
        }
    }



    /**
     * Get le champ id
     * @return number valeur du champ id
     */
    public function getId()
    {
        if( !is_null($this->nId) )
        {
            if( $this->nId==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nId;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('id'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nId;
        }
    }



    /**
     * Set le champ date_created
     * @param string $sDate_created nouvelle valeur pour le champ date_created
     */
    public function setDate_created($sDate_created)
    {
        if( is_null($sDate_created) ) $sDate_created='';
        $this->sDate_created = $sDate_created;
        $this->aDataSet["date_created"]=1;
    }



    /**
     * Get le champ date_created
     * @return string valeur du champ date_created
     */
    public function getDate_created()
    {
        if( !is_null($this->sDate_created) )
        {
            if( $this->sDate_created==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sDate_created;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('date_created'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sDate_created;
        }
    }



    /**
     * Set le champ date_amended
     * @param string $sDate_amended nouvelle valeur pour le champ date_amended
     */
    public function setDate_amended($sDate_amended)
    {
        if( is_null($sDate_amended) ) $sDate_amended='';
        $this->sDate_amended = $sDate_amended;
        $this->aDataSet["date_amended"]=1;
    }



    /**
     * Get le champ date_amended
     * @return string valeur du champ date_amended
     */
    public function getDate_amended()
    {
        if( !is_null($this->sDate_amended) )
        {
            if( $this->sDate_amended==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sDate_amended;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('date_amended'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sDate_amended;
        }
    }



    /**
     * Set le champ date_deleted
     * @param string $sDate_deleted nouvelle valeur pour le champ date_deleted
     */
    public function setDate_deleted($sDate_deleted)
    {
        if( is_null($sDate_deleted) ) $sDate_deleted='';
        $this->sDate_deleted = $sDate_deleted;
        $this->aDataSet["date_deleted"]=1;
    }



    /**
     * Get le champ date_deleted
     * @return string valeur du champ date_deleted
     */
    public function getDate_deleted()
    {
        if( !is_null($this->sDate_deleted) )
        {
            if( $this->sDate_deleted==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sDate_deleted;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('date_deleted'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sDate_deleted;
        }
    }



    /**
     * Set le champ reference
     * @param string $sReference nouvelle valeur pour le champ reference
     */
    public function setReference($sReference)
    {
        if( is_null($sReference) ) $sReference='';
        $this->sReference = $sReference;
        $this->aDataSet["reference"]=1;
    }



    /**
     * Get le champ reference
     * @return string valeur du champ reference
     */
    public function getReference()
    {
        if( !is_null($this->sReference) )
        {
            if( $this->sReference==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sReference;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('reference'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sReference;
        }
    }



    /**
     * Set le champ group_id
     * @param number $nGroup_id nouvelle valeur pour le champ group_id
     */
    public function setGroup_id($nGroup_id)
    {
        if( is_null($nGroup_id) ) $nGroup_id='';
        if( is_numeric($nGroup_id)  || $nGroup_id=='' )
        {
            $this->nGroup_id = $nGroup_id;
            $this->aDataSet["group_id"]=1;
        }
    }



    /**
     * Get le champ group_id
     * @return number valeur du champ group_id
     */
    public function getGroup_id()
    {
        if( !is_null($this->nGroup_id) )
        {
            if( $this->nGroup_id==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nGroup_id;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('group_id'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nGroup_id;
        }
    }

    public function Group()
    {
        if( $this->nGroup_id){
            $oGroup = new Group(array("id"=>$this->nGroup_id));
            $oGroup->HydrateFromBDD(array("*"));
            return $oGroup;
        }else{
            return NULL;
        }
    }


    /**
     * Set le champ user_id
     * @param number $nUser_id nouvelle valeur pour le champ user_id
     */
    public function setUser_id($nUser_id)
    {
        if( is_null($nUser_id) ) $nUser_id='';
        if( is_numeric($nUser_id)  || $nUser_id=='' )
        {
            $this->nUser_id = $nUser_id;
            $this->aDataSet["user_id"]=1;
        }
    }



    /**
     * Get le champ user_id
     * @return number valeur du champ user_id
     */
    public function getUser_id()
    {
        if( !is_null($this->nUser_id) )
        {
            if( $this->nUser_id==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nUser_id;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('user_id'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nUser_id;
        }
    }

    public function User()
    {
        if( $this->nUser_id){
            $oUser = new User(array("id"=>$this->nUser_id));
            $oUser->HydrateFromBDD(array("*"));
            return $oUser;
        }else{
            return NULL;
        }
    }


    /**
     * Set le champ amount
     * @param numeric $nAmount nouvelle valeur pour le champ amount
     */
    public function setAmount($nAmount)
    {
        if( is_null($nAmount) ) $nAmount='';
        if( is_numeric($nAmount)  || $nAmount=='' )
        {
            $this->nAmount = $nAmount;
            $this->aDataSet["amount"]=1;
        }
    }



    /**
     * Get le champ amount
     * @return numeric valeur du champ amount
     */
    public function getAmount()
    {
        if( !is_null($this->nAmount) )
        {
            if( $this->nAmount==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nAmount;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('amount'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nAmount;
        }
    }



    /**
     * Set le champ pledge_id
     * @param number $nPledge_id nouvelle valeur pour le champ pledge_id
     */
    public function setPledge_id($nPledge_id)
    {
        if( is_null($nPledge_id) ) $nPledge_id='';
        if( is_numeric($nPledge_id)  || $nPledge_id=='' )
        {
            $this->nPledge_id = $nPledge_id;
            $this->aDataSet["pledge_id"]=1;
        }
    }



    /**
     * Get le champ pledge_id
     * @return number valeur du champ pledge_id
     */
    public function getPledge_id()
    {
        if( !is_null($this->nPledge_id) )
        {
            if( $this->nPledge_id==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nPledge_id;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('pledge_id'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nPledge_id;
        }
    }

    public function Pledge()
    {
        if( $this->nPledge_id){
            $oPledge = new Pledge(array("id"=>$this->nPledge_id));
            $oPledge->HydrateFromBDD(array("*"));
            return $oPledge;
        }else{
            return NULL;
        }
    }


    /**
     * Set le champ path_file
     * @param string $sPath_file nouvelle valeur pour le champ path_file
     */
    public function setPath_file($sPath_file)
    {
        if( is_null($sPath_file) ) $sPath_file='';
        $this->sPath_file = $sPath_file;
        $this->aDataSet["path_file"]=1;
    }



    /**
     * Get le champ path_file
     * @return string valeur du champ path_file
     */
    public function getPath_file()
    {
        if( !is_null($this->sPath_file) )
        {
            if( $this->sPath_file==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sPath_file;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('path_file'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sPath_file;
        }
    }



    /**
     * Set le champ income
     * @param numeric $nIncome nouvelle valeur pour le champ income
     */
    public function setIncome($nIncome)
    {
        if( is_null($nIncome) ) $nIncome='';
        if( is_numeric($nIncome)  || $nIncome=='' )
        {
            $this->nIncome = $nIncome;
            $this->aDataSet["income"]=1;
        }
    }



    /**
     * Get le champ income
     * @return numeric valeur du champ income
     */
    public function getIncome()
    {
        if( !is_null($this->nIncome) )
        {
            if( $this->nIncome==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nIncome;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('income'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nIncome;
        }
    }



    /**
     * Set le champ expense
     * @param numeric $nExpense nouvelle valeur pour le champ expense
     */
    public function setExpense($nExpense)
    {
        if( is_null($nExpense) ) $nExpense='';
        if( is_numeric($nExpense)  || $nExpense=='' )
        {
            $this->nExpense = $nExpense;
            $this->aDataSet["expense"]=1;
        }
    }



    /**
     * Get le champ expense
     * @return numeric valeur du champ expense
     */
    public function getExpense()
    {
        if( !is_null($this->nExpense) )
        {
            if( $this->nExpense==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nExpense;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('expense'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nExpense;
        }
    }



    /**
     * Set le champ comment
     * @param string $sComment nouvelle valeur pour le champ comment
     */
    public function setComment($sComment)
    {
        if( is_null($sComment) ) $sComment='';
        $this->sComment = $sComment;
        $this->aDataSet["comment"]=1;
    }



    /**
     * Get le champ comment
     * @return string valeur du champ comment
     */
    public function getComment()
    {
        if( !is_null($this->sComment) )
        {
            if( $this->sComment==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sComment;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('comment'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sComment;
        }
    }

    /*
    ********************************************************************************************
    *                             DEBUT FONCTIONS PERSONNALISES                  	           *
    ********************************************************************************************
    */


    /*
    ********************************************************************************************
    *                             FIN FONCTIONS PERSONNALISES                     	           *
    ********************************************************************************************
    */


}