<?php
    require_once ('components/parts/session.php');

    if (isset($public) && $public || !isset($_SESSION['user']))
        include_once ('components/parts/site/header.ext.xml');
    else if (isset($public) && !$public || isset($_SESSION['user']))
        include_once ('components/parts/site/header.int.xml');
    else
        include_once ('components/parts/site/header.ext.xml');
