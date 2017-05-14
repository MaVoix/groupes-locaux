<?php

//url http://XXXXXX/pic/dons/[ID]/tirelire.png

//recupere l'image de fond
$img=new \claviska\SimpleImage("css/images/progressbar/background.png");

//dimension de l'image de départ
$width=$img->getWidth();
$height=$img->getHeight();

if(isset($_GET["id"])){

    //instancie le group
    $group = new Group(array("id" => intval($_GET["id"])));
    $group->hydrateFromBDD(array('*'));
    if ($group->getState() == "online") {

        //reglage de l'image
        $marginLeft=90;
        $marginRight=90;
        $marginTopForTextIncome=590;
        $marginTopForTextPledge=722;

        $widthBar=$width-$marginLeft-$marginRight;

        $overlayIncome=new \claviska\SimpleImage("css/images/progressbar/income.png");
        $overlayPledge=new \claviska\SimpleImage("css/images/progressbar/pledge.png");

        //calcul des pourcentages
        $amountPledge=$group->getAmount_plegde();
        $amountIncome=$group->getAmount_income();
        $amountMiss=$group->getAmount_target()-$amountIncome-$amountPledge;
        if($amountMiss<0){
            $amountMiss=0;
        }
        $pledge_percent = round($amountPledge*100/ $group->getAmount_target());
        $income_percent = round($amountIncome*100/ $group->getAmount_target());

        if($income_percent>100){
            $income_percent=99;
            $pledge_percent=1;
        }
        if($income_percent<=0){
            $income_percent=1;
        }
        if($pledge_percent<=0){
            $pledge_percent=1;
        }



        //crop la barre de progression INCOME
        $x1Income=$marginLeft;
        $x2Income=$marginLeft+round($width*$income_percent/100);
        $overlayIncome->crop($x1Income,0, $x2Income,$height);

        //crop la barre de progression PLEDGE
        $x1Pledge= $x2Income;
        $x2Pledge= $x2Income+round($width*$pledge_percent/100);
        $overlayPledge->crop($x1Pledge,0,$x2Pledge,$height);

        //colle les morceaux de barre
        $img->overlay( $overlayIncome, 'top left', 1, $x1Income, 0);
        $img->overlay( $overlayPledge, 'top left', 1, $x1Pledge, 0);

        //ajoute le texte
        $img->text("Dons : ".number_format($amountIncome,0,","," ")." €",array("fontFile"=> "css/images/progressbar/MyriadPro-Semibold.otf","size"=> 28,"color"=> "000000","anchor"=> "top left","xOffset"=>$x1Income+10,"yOffset"=>$marginTopForTextIncome ) );

        if($x2Pledge>$width/2){
            $img->text("Promesses : ".number_format($amountPledge,0,","," ")." €",array("fontFile"=> "css/images/progressbar/MyriadPro-Semibold.otf","size"=> 28,"color"=> "000000","anchor"=> "top right","xOffset"=>-($width-$x2Pledge),"yOffset"=>$marginTopForTextPledge ) );
        }else{
            $img->text("Promesses : ".number_format($amountPledge,0,","," ")." €",array("fontFile"=> "css/images/progressbar/MyriadPro-Semibold.otf","size"=> 28,"color"=> "000000","anchor"=> "top left","xOffset"=>$x2Pledge,"yOffset"=>$marginTopForTextPledge ) );
        }

        $img->text("Reste à financer : ".number_format($amountMiss,0,","," ")." € / ".number_format($group->getAmount_target(),0,","," ")." €",array("fontFile"=> "css/images/progressbar/MyriadPro-Semibold.otf","size"=> 28,"color"=> "000000","anchor"=> "top right","xOffset"=>-$marginRight,"yOffset"=>$marginTopForTextIncome ) );

        //text de la circo
        $img->text($group->getDepartement()->getCode()." - ".$group->getDepartement()->getName(),array("fontFile"=> "css/images/progressbar/MyriadPro-Semibold.otf","size"=> 40,"color"=> "000000","anchor"=> "center","xOffset"=>0,"yOffset"=>-90 ));
        $img->text("Circonscription n° ".$group->getCirconscription()->getNumber(),array("fontFile"=> "css/images/progressbar/MyriadPro-Regular.otf","size"=> 30,"color"=> "000000","anchor"=> "center","xOffset"=>0,"yOffset"=>-60 ) );

        //image
        //$overlayPic=new \claviska\SimpleImage($group->getPath_pic_fit());
        //$overlayPic->bestFit(200,200);
        //$img->overlay(  $overlayPic, 'top left', 1, $marginLeft, 25);

    }
}


$img->toScreen();
