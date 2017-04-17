<?php


session_start();


$bMaintenance=false;
if(file_exists('../maintenance.php')){
    require_once '../maintenance.php';
}else{
    echo "<h1>File maintenance.php not found. (see maintenance.sample.php for further details)</h1>";
}
if($bMaintenance){
    echo "<h1>Site en cours de maintenance</h1>";
}else{

    //composer loader
    require_once '../vendor/autoload.php';

    //config loader
    require_once '../config.php';

    require_once '../class/Liste.class.php';
    //class loader
    require_once '../class/Circonscription.class.php';
    require_once '../class/CirconscriptionListe.class.php';
    require_once '../class/Departement.class.php';
    require_once '../class/DepartementListe.class.php';
    require_once '../class/Group.class.php';
    require_once '../class/GroupListe.class.php';
    require_once '../class/Navigate.class.php';
    require_once '../class/People.class.php';
    require_once '../class/PeopleListe.class.php';
    require_once '../class/TwigAppExtension.class.php';
    require_once '../class/TwigExtension.class.php';
    require_once '../class/User.class.php';
    require_once '../class/UserListe.class.php';


    //service loader
    require_once '../services/App.class.php';
    require_once '../services/ConfigService.class.php';
    require_once '../services/DbLink.class.php';
    require_once '../services/Mail.class.php';
    require_once '../services/MessageService.class.php';
    require_once '../services/Mysql.class.php';
    require_once '../services/MysqlStatement.class.php';
    require_once '../services/SessionService.class.php';
    require_once '../services/Vars.class.php';

    //init app
   App::init();

}


