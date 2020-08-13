<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//** SESSION REQUIREMENTS */
    require_once ('components/applications/Session.php');

//** ROUTINES */
    $session = new Session();

if ((isset($public) && !$public && !isset($_SESSION['user']) || (isset($public) && !$public && isset($_SESSION['user']) && $_SESSION['user']->getGroup()==0)))
    header('Location: register.php');
else if (isset($public) && $public && isset($_SESSION['user']) && $_SESSION['user']->getGroup()!=0)
    header("Location: index.php");

if (isset($restricted) && isset($_SESSION['user']))
    if($restricted==1 && $_SESSION['user']->getGroup()<2)
        header('Location: 403.php');



