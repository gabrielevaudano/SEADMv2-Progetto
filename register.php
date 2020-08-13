<?php
$public = true;
require_once ('components/parts/header.php');

try {
    if (isset($_POST['email']) && isset($_POST['resend-verification-link']) && $_POST['resend-verification-link'] == 1) {
        if ($session->resendData($_POST['email']))
            include('components/parts/site/register.resend.xml');
        else
            throw new RuntimeException("La procedura di richiesta non è andata a buon fine.");
    }
    else if (isset($_POST['email']) && isset($_POST['password']))
    {
        if (!preg_match('/^(?:(?=.*?[A-Z])(?:(?=.*?[0-9])(?=.*?[-!@#$%^&*()_[\]{},.<>+=])|(?=.*?[a-z])(?:(?=.*?[0-9])|(?=.*?[-!@#$%^&*()_[\]{},.<>+=])))|(?=.*?[a-z])(?=.*?[0-9])(?=.*?[-!@#$%^&*()_[\]{},.<>+=]))[A-Za-z0-9!@#$%^&*()_[\]{},.<>+=-]{7,50}$/', $_POST['password']))
            throw new RuntimeException("La password inserita non incontra gli standard richiesti di sicurezza.");
        if (!isset($_POST['gender']))
            throw new RuntimeException("Non è stato inserito un genere valido");
        if (!isset($_POST['knowledge']))
            throw new RuntimeException("Non è stato inserito un livello di conoscenza valido");
        if (!isset($_POST['age']))
            throw new RuntimeException("Non è stato inserito un range d'età valido");

        $user = new UserInfo($_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), null, $_POST['age'], $_POST['gender'], $_POST['knowledge'], 1);

        if ($session->register($user))
            include('components/parts/site/register.intermediate.xml');
        else
            throw new RuntimeException("C'è stato un problema imprevisto. Ti consigliamo di ritentare la procedura di registrazione tra un paio di minuti. Se il problema persiste, contatta il centro assistenza.");

    }
    else if (isset($_GET['auth']) && isset($_GET['token']))
    {
        if ($session->validateRegister($_GET['auth'], $_GET['token']))
            include ('components/parts/site/register.validate.xml');
        else
            throw new RuntimeException("Non è stato possibile completare la procedura di verifica dell'e-mail!\nTi consigliamo di ritentare la procedura di registrazione tra un paio di minuti. Se il problema persiste, contatta il centro assistenza.");
    }
    else
    {
        include('components/parts/site/register.start.xml');
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    include('components/parts/site/register.error.xml');
}

require_once ('components/parts/footer.php');



