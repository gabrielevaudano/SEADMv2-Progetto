<?php
    $public = true;
    require_once ('components/parts/header.php');

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $code = $session->login($_REQUEST['email'], $_REQUEST['password']);

        if(empty($code))
            $code = "&Egrave; stato riscontrato un errore generico di comunicazione. Ritenta la procedura";

        if ($code !== true)
            include('components/parts/site/login.unsuccessful.xml');
        else
            header("Location: index.php");
    }
    else
        include ('components/parts/site/login.xml');

    include ('components/parts/footer.php');
?>
