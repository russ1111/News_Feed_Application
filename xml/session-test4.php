<?php
//session-test4.php


//must use this to see session data at all
session_start();


if(!isset($_SESSION['BallPlayers']))
{//make sure the array exists
    $_SESSION['BallPlayers'] = array();
}

$BallPlayers[] =  new BallPlayer('Jackie Robinson', 42);
$BallPlayers[] =  new BallPlayer('Boris Yeltsin', 54);


$_SESSION['BallPlayers'][] = $BallPlayers;

//add data
$sum = 0;

foreach ($_SESSION['BallPlayers'] as $ballplayer) {
           $sum += $ballplayer->$Homers;
}

echo $sum;




/*
if(isset($_SESSION['BallPlayers']))
{//show color
    echo '<pre>';
    echo var_dump($_SESSION);
    echo '</pre>';
}else{//apologize
    echo 'No favorite ball player selected'; 
}
*/


class BallPlayer
{
    public $Name = '';
    public $Homers = 0;
    
    public function __construct($Name, $Homers)
    {
        $this->Name = $Name;
        $this->Homers = $Homers;
        
    }


}
