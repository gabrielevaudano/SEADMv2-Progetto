<?php
    $public = false;
    require_once ('components/parts/session.php');

    //** PROCEDO AL LOGOUT */
    try {
        if ($session->logout())
            header("Location: login.php");
        else
            include ('components/parts/site/logout.unsuccessful.xml');
    } catch (Exception $e) {
        return false;
    }
