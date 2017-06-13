<?php	/**
 * Class Group
 */
class Group	{

    private $aDataNonHydrate = array();
    private $aDataSet = array();
    private $callHydrateFromBDDOnGet = 0;

    private $_sDbInstance = null;

    private $nId;
    private $sName;
    private $sDate_created;
    private $sDate_amended;
    private $sDate_deleted;
    private $sState;
    private $nDepartement_id;
    private $nCirconscription_id;
    private $sPath_pic;
    private $sBank_name;
    private $sBank_city;
    private $sIban;
    private $sBic;
    private $sCheque_payable_to;
    private $sEmail;
    private $sFacebook_page;
    private $sFacebook_group;
    private $sTwitter;
    private $sComment;
    private $sPresentation;
    private $sMap_url;
    private $nAmount_promises;
    private $nAmount_donations;
    private $nPosters;
    private $nBallots;
    private $nPost_expenses;
    private $nBanking_fees;
    private $nSmall_expenses;
    private $nEmailing_expenses;
    private $nProfessions_de_foi;
    private $nAmount_target;
    private $sKey_edit;


    /**
     * Constructeur
     * @param array $aParam tableau de parametres ( clé "id" pour instancier un group avec un id précis )
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
                    //echo "<br />Group->$sMethode() n'existe pas!";
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
            $aData=DbLink::getInstance($this->_sDbInstance)->selectForHydrate($this->getId(),"group",$aFields);

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
        $oReq=DbLink::getInstance($this->_sDbInstance)->prepare('DELETE FROM '."group".' WHERE  id=:id ');
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
        $oReq=DbLink::getInstance($sDbInstance)->prepare('SELECT id FROM '."group".' WHERE  id=:id ');
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
        if(isset($this->aDataSet["name"]))
        {
            $aData["name"]=$this->getName();
        }

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

        if(isset($this->aDataSet["state"]))
        {
            $aData["state"]=$this->getState();
        }

        if(isset($this->aDataSet["departement_id"]))
        {
            $aData["departement_id"]=$this->getDepartement_id();
        }

        if(isset($this->aDataSet["circonscription_id"]))
        {
            $aData["circonscription_id"]=$this->getCirconscription_id();
        }

        if(isset($this->aDataSet["path_pic"]))
        {
            $aData["path_pic"]=$this->getPath_pic();
        }

        if(isset($this->aDataSet["bank_name"]))
        {
            $aData["bank_name"]=$this->getBank_name();
        }

        if(isset($this->aDataSet["bank_city"]))
        {
            $aData["bank_city"]=$this->getBank_city();
        }

        if(isset($this->aDataSet["iban"]))
        {
            $aData["iban"]=$this->getIban();
        }

        if(isset($this->aDataSet["bic"]))
        {
            $aData["bic"]=$this->getBic();
        }

        if(isset($this->aDataSet["cheque_payable_to"]))
        {
            $aData["cheque_payable_to"]=$this->getCheque_payable_to();
        }

        if(isset($this->aDataSet["email"]))
        {
            $aData["email"]=$this->getEmail();
        }

        if(isset($this->aDataSet["facebook_page"]))
        {
            $aData["facebook_page"]=$this->getFacebook_page();
        }

        if(isset($this->aDataSet["facebook_group"]))
        {
            $aData["facebook_group"]=$this->getFacebook_group();
        }

        if(isset($this->aDataSet["twitter"]))
        {
            $aData["twitter"]=$this->getTwitter();
        }

        if(isset($this->aDataSet["comment"]))
        {
            $aData["comment"]=$this->getComment();
        }

        if(isset($this->aDataSet["presentation"]))
        {
            $aData["presentation"]=$this->getPresentation();
        }

        if(isset($this->aDataSet["map_url"]))
        {
            $aData["map_url"]=$this->getMap_url();
        }

        if(isset($this->aDataSet["amount_promises"]))
        {
            $aData["amount_promises"]=$this->getAmount_promises();
        }

        if(isset($this->aDataSet["amount_donations"]))
        {
            $aData["amount_donations"]=$this->getAmount_donations();
        }

        if(isset($this->aDataSet["posters"]))
        {
            $aData["posters"]=$this->getPosters();
        }

        if(isset($this->aDataSet["ballots"]))
        {
            $aData["ballots"]=$this->getBallots();
        }

        if(isset($this->aDataSet["post_expenses"]))
        {
            $aData["post_expenses"]=$this->getPost_expenses();
        }

        if(isset($this->aDataSet["banking_fees"]))
        {
            $aData["banking_fees"]=$this->getBanking_fees();
        }

        if(isset($this->aDataSet["small_expenses"]))
        {
            $aData["small_expenses"]=$this->getSmall_expenses();
        }

        if(isset($this->aDataSet["emailing_expenses"]))
        {
            $aData["emailing_expenses"]=$this->getEmailing_expenses();
        }

        if(isset($this->aDataSet["professions_de_foi"]))
        {
            $aData["professions_de_foi"]=$this->getProfessions_de_foi();
        }

        if(isset($this->aDataSet["amount_target"]))
        {
            $aData["amount_target"]=$this->getAmount_target();
        }

        if(isset($this->aDataSet["key_edit"]))
        {
            $aData["key_edit"]=$this->getKey_edit();
        }

        if($this->getId()>0)
        {
            DbLink::getInstance($this->_sDbInstance)->update("group",$aData,' id="'.$this->getId().'" ');
        }
        else
        {
            $this->setId(DbLink::getInstance($this->_sDbInstance)->insert("group",$aData));
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
        $this->setName(NULL);
        $this->setDate_created(NULL);
        $this->setDate_amended(NULL);
        $this->setDate_deleted(NULL);
        $this->setState(NULL);
        $this->setDepartement_id(NULL);
        $this->setCirconscription_id(NULL);
        $this->setPath_pic(NULL);
        $this->setBank_name(NULL);
        $this->setBank_city(NULL);
        $this->setIban(NULL);
        $this->setBic(NULL);
        $this->setCheque_payable_to(NULL);
        $this->setEmail(NULL);
        $this->setFacebook_page(NULL);
        $this->setFacebook_group(NULL);
        $this->setTwitter(NULL);
        $this->setComment(NULL);
        $this->setPresentation(NULL);
        $this->setMap_url(NULL);
        $this->setAmount_promises(0);
        $this->setAmount_donations(0);
        $this->setPosters(NULL);
        $this->setBallots(NULL);
        $this->setPost_expenses(0);
        $this->setBanking_fees(0);
        $this->setSmall_expenses(0);
        $this->setEmailing_expenses(NULL);
        $this->setProfessions_de_foi(NULL);
        $this->setAmount_target(NULL);
        $this->setKey_edit(NULL);
    }

    /**
     * Renvoie l'objet sous forme de chaine de caractère
     */
    public function __toString()
    {
        $aObjet = [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "date_created" => $this->getDate_created(),
            "date_amended" => $this->getDate_amended(),
            "date_deleted" => $this->getDate_deleted(),
            "state" => $this->getState(),
            "departement_id" => $this->getDepartement_id(),
            "circonscription_id" => $this->getCirconscription_id(),
            "path_pic" => $this->getPath_pic(),
            "bank_name" => $this->getBank_name(),
            "bank_city" => $this->getBank_city(),
            "iban" => $this->getIban(),
            "bic" => $this->getBic(),
            "cheque_payable_to" => $this->getCheque_payable_to(),
            "email" => $this->getEmail(),
            "facebook_page" => $this->getFacebook_page(),
            "facebook_group" => $this->getFacebook_group(),
            "twitter" => $this->getTwitter(),
            "comment" => $this->getComment(),
            "presentation" => $this->getPresentation(),
            "map_url" => $this->getMap_url(),
            "amount_promises" => $this->getAmount_promises(),
            "amount_donations" => $this->getAmount_donations(),
            "posters" => $this->getPosters(),
            "ballots" => $this->getBallots(),
            "post_expenses" => $this->getPost_expenses(),
            "banking_fees" => $this->getBanking_fees(),
            "small_expenses" => $this->getSmall_expenses(),
            "emailing_expenses" => $this->getEmailing_expenses(),
            "professions_de_foi" => $this->getProfessions_de_foi(),
            "amount_target" => $this->getAmount_target(),
            "key_edit" => $this->getKey_edit()
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
     * Set le champ name
     * @param string $sName nouvelle valeur pour le champ name
     */
    public function setName($sName)
    {
        if( is_null($sName) ) $sName='';
        $this->sName = $sName;
        $this->aDataSet["name"]=1;
    }



    /**
     * Get le champ name
     * @return string valeur du champ name
     */
    public function getName()
    {
        if( !is_null($this->sName) )
        {
            if( $this->sName==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sName;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('name'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sName;
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
     * Set le champ state
     * @param string $sState nouvelle valeur pour le champ state
     */
    public function setState($sState)
    {
        if( is_null($sState) ) $sState='';
        $this->sState = $sState;
        $this->aDataSet["state"]=1;
    }



    /**
     * Get le champ state
     * @return string valeur du champ state
     */
    public function getState()
    {
        if( !is_null($this->sState) )
        {
            if( $this->sState==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sState;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('state'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sState;
        }
    }



    /**
     * Set le champ departement_id
     * @param number $nDepartement_id nouvelle valeur pour le champ departement_id
     */
    public function setDepartement_id($nDepartement_id)
    {
        if( is_null($nDepartement_id) ) $nDepartement_id='';
        if( is_numeric($nDepartement_id)  || $nDepartement_id=='' )
        {
            $this->nDepartement_id = $nDepartement_id;
            $this->aDataSet["departement_id"]=1;
        }
    }



    /**
     * Get le champ departement_id
     * @return number valeur du champ departement_id
     */
    public function getDepartement_id()
    {
        if( !is_null($this->nDepartement_id) )
        {
            if( $this->nDepartement_id==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nDepartement_id;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('departement_id'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nDepartement_id;
        }
    }

    public function Departement()
    {
        if( $this->nDepartement_id){
            $oDepartement = new Departement(array("id"=>$this->nDepartement_id));
            $oDepartement->HydrateFromBDD(array("*"));
            return $oDepartement;
        }else{
            return NULL;
        }
    }


    /**
     * Set le champ circonscription_id
     * @param number $nCirconscription_id nouvelle valeur pour le champ circonscription_id
     */
    public function setCirconscription_id($nCirconscription_id)
    {
        if( is_null($nCirconscription_id) ) $nCirconscription_id='';
        if( is_numeric($nCirconscription_id)  || $nCirconscription_id=='' )
        {
            $this->nCirconscription_id = $nCirconscription_id;
            $this->aDataSet["circonscription_id"]=1;
        }
    }



    /**
     * Get le champ circonscription_id
     * @return number valeur du champ circonscription_id
     */
    public function getCirconscription_id()
    {
        if( !is_null($this->nCirconscription_id) )
        {
            if( $this->nCirconscription_id==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nCirconscription_id;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('circonscription_id'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nCirconscription_id;
        }
    }

    public function Circonscription()
    {
        if( $this->nCirconscription_id){
            $oCirconscription = new Circonscription(array("id"=>$this->nCirconscription_id));
            $oCirconscription->HydrateFromBDD(array("*"));
            return $oCirconscription;
        }else{
            return NULL;
        }
    }


    /**
     * Set le champ path_pic
     * @param string $sPath_pic nouvelle valeur pour le champ path_pic
     */
    public function setPath_pic($sPath_pic)
    {
        if( is_null($sPath_pic) ) $sPath_pic='';
        $this->sPath_pic = $sPath_pic;
        $this->aDataSet["path_pic"]=1;
    }



    /**
     * Get le champ path_pic
     * @return string valeur du champ path_pic
     */
    public function getPath_pic()
    {
        if( !is_null($this->sPath_pic) )
        {
            if( $this->sPath_pic==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sPath_pic;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('path_pic'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sPath_pic;
        }
    }



    /**
     * Set le champ bank_name
     * @param string $sBank_name nouvelle valeur pour le champ bank_name
     */
    public function setBank_name($sBank_name)
    {
        if( is_null($sBank_name) ) $sBank_name='';
        $this->sBank_name = $sBank_name;
        $this->aDataSet["bank_name"]=1;
    }



    /**
     * Get le champ bank_name
     * @return string valeur du champ bank_name
     */
    public function getBank_name()
    {
        if( !is_null($this->sBank_name) )
        {
            if( $this->sBank_name==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sBank_name;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('bank_name'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sBank_name;
        }
    }



    /**
     * Set le champ bank_city
     * @param string $sBank_city nouvelle valeur pour le champ bank_city
     */
    public function setBank_city($sBank_city)
    {
        if( is_null($sBank_city) ) $sBank_city='';
        $this->sBank_city = $sBank_city;
        $this->aDataSet["bank_city"]=1;
    }



    /**
     * Get le champ bank_city
     * @return string valeur du champ bank_city
     */
    public function getBank_city()
    {
        if( !is_null($this->sBank_city) )
        {
            if( $this->sBank_city==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sBank_city;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('bank_city'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sBank_city;
        }
    }



    /**
     * Set le champ iban
     * @param string $sIban nouvelle valeur pour le champ iban
     */
    public function setIban($sIban)
    {
        if( is_null($sIban) ) $sIban='';
        $this->sIban = $sIban;
        $this->aDataSet["iban"]=1;
    }



    /**
     * Get le champ iban
     * @return string valeur du champ iban
     */
    public function getIban()
    {
        if( !is_null($this->sIban) )
        {
            if( $this->sIban==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sIban;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('iban'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sIban;
        }
    }



    /**
     * Set le champ bic
     * @param string $sBic nouvelle valeur pour le champ bic
     */
    public function setBic($sBic)
    {
        if( is_null($sBic) ) $sBic='';
        $this->sBic = $sBic;
        $this->aDataSet["bic"]=1;
    }



    /**
     * Get le champ bic
     * @return string valeur du champ bic
     */
    public function getBic()
    {
        if( !is_null($this->sBic) )
        {
            if( $this->sBic==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sBic;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('bic'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sBic;
        }
    }



    /**
     * Set le champ cheque_payable_to
     * @param string $sCheque_payable_to nouvelle valeur pour le champ cheque_payable_to
     */
    public function setCheque_payable_to($sCheque_payable_to)
    {
        if( is_null($sCheque_payable_to) ) $sCheque_payable_to='';
        $this->sCheque_payable_to = $sCheque_payable_to;
        $this->aDataSet["cheque_payable_to"]=1;
    }



    /**
     * Get le champ cheque_payable_to
     * @return string valeur du champ cheque_payable_to
     */
    public function getCheque_payable_to()
    {
        if( !is_null($this->sCheque_payable_to) )
        {
            if( $this->sCheque_payable_to==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sCheque_payable_to;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('cheque_payable_to'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sCheque_payable_to;
        }
    }



    /**
     * Set le champ email
     * @param string $sEmail nouvelle valeur pour le champ email
     */
    public function setEmail($sEmail)
    {
        if( is_null($sEmail) ) $sEmail='';
        $this->sEmail = $sEmail;
        $this->aDataSet["email"]=1;
    }



    /**
     * Get le champ email
     * @return string valeur du champ email
     */
    public function getEmail()
    {
        if( !is_null($this->sEmail) )
        {
            if( $this->sEmail==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sEmail;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('email'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sEmail;
        }
    }



    /**
     * Set le champ facebook_page
     * @param string $sFacebook_page nouvelle valeur pour le champ facebook_page
     */
    public function setFacebook_page($sFacebook_page)
    {
        if( is_null($sFacebook_page) ) $sFacebook_page='';
        $this->sFacebook_page = $sFacebook_page;
        $this->aDataSet["facebook_page"]=1;
    }



    /**
     * Get le champ facebook_page
     * @return string valeur du champ facebook_page
     */
    public function getFacebook_page()
    {
        if( !is_null($this->sFacebook_page) )
        {
            if( $this->sFacebook_page==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sFacebook_page;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('facebook_page'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sFacebook_page;
        }
    }



    /**
     * Set le champ facebook_group
     * @param string $sFacebook_group nouvelle valeur pour le champ facebook_group
     */
    public function setFacebook_group($sFacebook_group)
    {
        if( is_null($sFacebook_group) ) $sFacebook_group='';
        $this->sFacebook_group = $sFacebook_group;
        $this->aDataSet["facebook_group"]=1;
    }



    /**
     * Get le champ facebook_group
     * @return string valeur du champ facebook_group
     */
    public function getFacebook_group()
    {
        if( !is_null($this->sFacebook_group) )
        {
            if( $this->sFacebook_group==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sFacebook_group;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('facebook_group'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sFacebook_group;
        }
    }



    /**
     * Set le champ twitter
     * @param string $sTwitter nouvelle valeur pour le champ twitter
     */
    public function setTwitter($sTwitter)
    {
        if( is_null($sTwitter) ) $sTwitter='';
        $this->sTwitter = $sTwitter;
        $this->aDataSet["twitter"]=1;
    }



    /**
     * Get le champ twitter
     * @return string valeur du champ twitter
     */
    public function getTwitter()
    {
        if( !is_null($this->sTwitter) )
        {
            if( $this->sTwitter==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sTwitter;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('twitter'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sTwitter;
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



    /**
     * Set le champ presentation
     * @param string $sPresentation nouvelle valeur pour le champ presentation
     */
    public function setPresentation($sPresentation)
    {
        if( is_null($sPresentation) ) $sPresentation='';
        $this->sPresentation = $sPresentation;
        $this->aDataSet["presentation"]=1;
    }



    /**
     * Get le champ presentation
     * @return string valeur du champ presentation
     */
    public function getPresentation()
    {
        if( !is_null($this->sPresentation) )
        {
            if( $this->sPresentation==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sPresentation;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('presentation'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sPresentation;
        }
    }



    /**
     * Set le champ map_url
     * @param string $sMap_url nouvelle valeur pour le champ map_url
     */
    public function setMap_url($sMap_url)
    {
        if( is_null($sMap_url) ) $sMap_url='';
        $this->sMap_url = $sMap_url;
        $this->aDataSet["map_url"]=1;
    }



    /**
     * Get le champ map_url
     * @return string valeur du champ map_url
     */
    public function getMap_url()
    {
        if( !is_null($this->sMap_url) )
        {
            if( $this->sMap_url==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sMap_url;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('map_url'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sMap_url;
        }
    }



    /**
     * Set le champ amount_promises
     * @param numeric $nAmount_promises nouvelle valeur pour le champ amount_promises
     */
    public function setAmount_promises($nAmount_promises)
    {
        if( is_null($nAmount_promises) ) $nAmount_promises='';
        if( is_numeric($nAmount_promises)  || $nAmount_promises=='' )
        {
            $this->nAmount_promises = $nAmount_promises;
            $this->aDataSet["amount_promises"]=1;
        }
    }



    /**
     * Get le champ amount_promises
     * @return numeric valeur du champ amount_promises
     */
    public function getAmount_promises()
    {
        if( !is_null($this->nAmount_promises) )
        {
            if( $this->nAmount_promises==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nAmount_promises;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('amount_promises'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nAmount_promises;
        }
    }



    /**
     * Set le champ amount_donations
     * @param numeric $nAmount_donations nouvelle valeur pour le champ amount_donations
     */
    public function setAmount_donations($nAmount_donations)
    {
        if( is_null($nAmount_donations) ) $nAmount_donations='';
        if( is_numeric($nAmount_donations)  || $nAmount_donations=='' )
        {
            $this->nAmount_donations = $nAmount_donations;
            $this->aDataSet["amount_donations"]=1;
        }
    }



    /**
     * Get le champ amount_donations
     * @return numeric valeur du champ amount_donations
     */
    public function getAmount_donations()
    {
        if( !is_null($this->nAmount_donations) )
        {
            if( $this->nAmount_donations==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nAmount_donations;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('amount_donations'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nAmount_donations;
        }
    }



    /**
     * Set le champ posters
     * @param number $nPosters nouvelle valeur pour le champ posters
     */
    public function setPosters($nPosters)
    {
        if( is_null($nPosters) ) $nPosters='';
        if( is_numeric($nPosters)  || $nPosters=='' )
        {
            $this->nPosters = $nPosters;
            $this->aDataSet["posters"]=1;
        }
    }



    /**
     * Get le champ posters
     * @return number valeur du champ posters
     */
    public function getPosters()
    {
        if( !is_null($this->nPosters) )
        {
            if( $this->nPosters==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nPosters;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('posters'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nPosters;
        }
    }



    /**
     * Set le champ ballots
     * @param number $nBallots nouvelle valeur pour le champ ballots
     */
    public function setBallots($nBallots)
    {
        if( is_null($nBallots) ) $nBallots='';
        if( is_numeric($nBallots)  || $nBallots=='' )
        {
            $this->nBallots = $nBallots;
            $this->aDataSet["ballots"]=1;
        }
    }



    /**
     * Get le champ ballots
     * @return number valeur du champ ballots
     */
    public function getBallots()
    {
        if( !is_null($this->nBallots) )
        {
            if( $this->nBallots==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nBallots;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('ballots'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nBallots;
        }
    }



    /**
     * Set le champ post_expenses
     * @param numeric $nPost_expenses nouvelle valeur pour le champ post_expenses
     */
    public function setPost_expenses($nPost_expenses)
    {
        if( is_null($nPost_expenses) ) $nPost_expenses='';
        if( is_numeric($nPost_expenses)  || $nPost_expenses=='' )
        {
            $this->nPost_expenses = $nPost_expenses;
            $this->aDataSet["post_expenses"]=1;
        }
    }



    /**
     * Get le champ post_expenses
     * @return numeric valeur du champ post_expenses
     */
    public function getPost_expenses()
    {
        if( !is_null($this->nPost_expenses) )
        {
            if( $this->nPost_expenses==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nPost_expenses;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('post_expenses'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nPost_expenses;
        }
    }



    /**
     * Set le champ banking_fees
     * @param numeric $nBanking_fees nouvelle valeur pour le champ banking_fees
     */
    public function setBanking_fees($nBanking_fees)
    {
        if( is_null($nBanking_fees) ) $nBanking_fees='';
        if( is_numeric($nBanking_fees)  || $nBanking_fees=='' )
        {
            $this->nBanking_fees = $nBanking_fees;
            $this->aDataSet["banking_fees"]=1;
        }
    }



    /**
     * Get le champ banking_fees
     * @return numeric valeur du champ banking_fees
     */
    public function getBanking_fees()
    {
        if( !is_null($this->nBanking_fees) )
        {
            if( $this->nBanking_fees==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nBanking_fees;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('banking_fees'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nBanking_fees;
        }
    }



    /**
     * Set le champ small_expenses
     * @param numeric $nSmall_expenses nouvelle valeur pour le champ small_expenses
     */
    public function setSmall_expenses($nSmall_expenses)
    {
        if( is_null($nSmall_expenses) ) $nSmall_expenses='';
        if( is_numeric($nSmall_expenses)  || $nSmall_expenses=='' )
        {
            $this->nSmall_expenses = $nSmall_expenses;
            $this->aDataSet["small_expenses"]=1;
        }
    }



    /**
     * Get le champ small_expenses
     * @return numeric valeur du champ small_expenses
     */
    public function getSmall_expenses()
    {
        if( !is_null($this->nSmall_expenses) )
        {
            if( $this->nSmall_expenses==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nSmall_expenses;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('small_expenses'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nSmall_expenses;
        }
    }



    /**
     * Set le champ emailing_expenses
     * @param number $nEmailing_expenses nouvelle valeur pour le champ emailing_expenses
     */
    public function setEmailing_expenses($nEmailing_expenses)
    {
        if( is_null($nEmailing_expenses) ) $nEmailing_expenses='';
        if( is_numeric($nEmailing_expenses)  || $nEmailing_expenses=='' )
        {
            $this->nEmailing_expenses = $nEmailing_expenses;
            $this->aDataSet["emailing_expenses"]=1;
        }
    }



    /**
     * Get le champ emailing_expenses
     * @return number valeur du champ emailing_expenses
     */
    public function getEmailing_expenses()
    {
        if( !is_null($this->nEmailing_expenses) )
        {
            if( $this->nEmailing_expenses==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nEmailing_expenses;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('emailing_expenses'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nEmailing_expenses;
        }
    }



    /**
     * Set le champ professions_de_foi
     * @param number $nProfessions_de_foi nouvelle valeur pour le champ professions_de_foi
     */
    public function setProfessions_de_foi($nProfessions_de_foi)
    {
        if( is_null($nProfessions_de_foi) ) $nProfessions_de_foi='';
        if( is_numeric($nProfessions_de_foi)  || $nProfessions_de_foi=='' )
        {
            $this->nProfessions_de_foi = $nProfessions_de_foi;
            $this->aDataSet["professions_de_foi"]=1;
        }
    }



    /**
     * Get le champ professions_de_foi
     * @return number valeur du champ professions_de_foi
     */
    public function getProfessions_de_foi()
    {
        if( !is_null($this->nProfessions_de_foi) )
        {
            if( $this->nProfessions_de_foi==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nProfessions_de_foi;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('professions_de_foi'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nProfessions_de_foi;
        }
    }



    /**
     * Set le champ amount_target
     * @param number $nAmount_target nouvelle valeur pour le champ amount_target
     */
    public function setAmount_target($nAmount_target)
    {
        if( is_null($nAmount_target) ) $nAmount_target='';
        if( is_numeric($nAmount_target)  || $nAmount_target=='' )
        {
            $this->nAmount_target = $nAmount_target;
            $this->aDataSet["amount_target"]=1;
        }
    }



    /**
     * Get le champ amount_target
     * @return number valeur du champ amount_target
     */
    public function getAmount_target()
    {
        if( !is_null($this->nAmount_target) )
        {
            if( $this->nAmount_target==='' )
            {
                return NULL;
            }
            else
            {
                return $this->nAmount_target;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('amount_target'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->nAmount_target;
        }
    }



    /**
     * Set le champ key_edit
     * @param string $sKey_edit nouvelle valeur pour le champ key_edit
     */
    public function setKey_edit($sKey_edit)
    {
        if( is_null($sKey_edit) ) $sKey_edit='';
        $this->sKey_edit = $sKey_edit;
        $this->aDataSet["key_edit"]=1;
    }



    /**
     * Get le champ key_edit
     * @return string valeur du champ key_edit
     */
    public function getKey_edit()
    {
        if( !is_null($this->sKey_edit) )
        {
            if( $this->sKey_edit==='' )
            {
                return NULL;
            }
            else
            {
                return $this->sKey_edit;
            }
        }
        else
        {
            $this->hydrateFromBDD(array('key_edit'));
            $this->callHydrateFromBDDOnGet++;
            if($this->callHydrateFromBDDOnGet>10)
            {
                echo "<br />WARNING : trop d'appel en base depuis l'accesseur ". __CLASS__ ."::". __FUNCTION__ ."";
            }
            return $this->sKey_edit;
        }
    }

    /*
    ********************************************************************************************
    *                             DEBUT FONCTIONS PERSONNALISES                  	           *
    ********************************************************************************************
    */

    public function getMandataire(){
        $oListeUser= new UserListe();
        $oListeUser->applyRules4Group($this->getId(),"mandataire");
        $aUsers=$oListeUser->getPage();
        if(count($aUsers)){
            $user=new User(array("id"=>$aUsers[0]["id"]));
            $user->hydrate($aUsers[0]);
        }else{
            $user=new User();
        }
        return  $user;
    }

    public function getCandidat(){
        $oListeUser= new UserListe();
        $oListeUser->applyRules4Group($this->getId(),"candidat");
        $aUsers=$oListeUser->getPage();
        if(count($aUsers)){
            $user=new User(array("id"=>$aUsers[0]["id"]));
            $user->hydrate($aUsers[0]);
        }else{
            $user=new User();
        }
        return  $user;
    }

    public function getListeMember(){
        $oListeUser= new UserListe();
        $oListeUser->applyRules4Group($this->getId(),"membre");
        return $oListeUser;
    }

    public function getPath_pic_fit()
    {
     if($this->getPath_pic()!=""){
         return str_replace(basename($this->getPath_pic()),"photo-fit.jpg",$this->getPath_pic());
     }else{
         return "css/images/go.jpg";
     }

    }

    public function getAmount_plegde(){
        $amount=0;
        $pledgeListe= new PledgeListe();
        //todo : sum avec jointure pour améliorer performance
        $pledgeListe->applyRules4GroupCurrent($this->getId());
        $pledges=$pledgeListe->getPage();
        foreach($pledges as $pledge){
            $amount+=doubleval($pledge["amount"]);
        }
        return $amount;
    }
    public function getAmount_income(){
        $amount=0;
        $transactionListe= new TransactionListe();
        //todo : sum avec jointure pour améliorer performance
        $transactionListe->applyRules4Group($this->getId());
        $transactions=$transactionListe->getPage();
        foreach($transactions as $transaction){
            $amount+=doubleval($transaction["income"]);
        }
        return $amount;

    }

    /* Lien TWIG vers les accesseurs */
    public function getDepartement(){
        return $this->Departement();
    }
    public function getCirconscription(){
        return $this->Circonscription();
    }

    /*
    ********************************************************************************************
    *                             FIN FONCTIONS PERSONNALISES                     	           *
    ********************************************************************************************
    */


}
