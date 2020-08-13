
<?php
$public = true;
$main = null;

require_once ('components/parts/header.php');

try {
    if (isset($_GET['token']) && isset($_GET['auth'])) {
        include('components/parts/site/forgot-completion.xml');

    } else if (isset($_POST['auth']) && isset($_POST['token']) && isset($_POST['new-password'])) {
        if (!preg_match('/^(?:(?=.*?[A-Z])(?:(?=.*?[0-9])(?=.*?[-!@#$%^&*()_[\]{},.<>+=])|(?=.*?[a-z])(?:(?=.*?[0-9])|(?=.*?[-!@#$%^&*()_[\]{},.<>+=])))|(?=.*?[a-z])(?=.*?[0-9])(?=.*?[-!@#$%^&*()_[\]{},.<>+=]))[A-Za-z0-9!@#$%^&*()_[\]{},.<>+=-]{7,50}$/', $_POST['new-password'])) {
            throw new RuntimeException("La password inserita non incontra gli standard richiesti di sicurezza.");
        }

        if ($session->changePassword($_POST['auth'], $_POST['token'], password_hash($_POST['new-password'], PASSWORD_DEFAULT)))
            include('components/parts/site/forgot-close.xml');
        else
            throw new RuntimeException("Non è stato possibile completare la richiesta di cambio della password.");


    } else if (isset($_POST['email']) && isset($_POST['proceed'])) {
        if ($session->isUserExists($_POST['email'])) {
            if ($session->resendForgotData($_POST['email'])) {
                include('components/parts/site/forgot-temp-completion.xml');
            }
        } else {
            throw new RuntimeException("L'utente selezionato non esiste, pertanto non è possibile completare la procedura di aggiornamento della password.");
        }

    } else {
        include('components/parts/site/forgot-start.xml');
    }
} catch (ArithmeticError $ce) {
    $error = $ce->getMessage();

    echo <<<HTML
<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
    <div class="col-lg-6">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-2">Token non valido</h1>
                <p class="mb-4">$error</p>
            </div>
            <hr />
            <div class="text-center">
                <a class="small" href="register.php">Crea un Account!</a>
            </div>
            <div class="text-center">
                <a class="small" href="login.php">Hai già un account? Accesso!</a>
            </div>
        </div>
    </div>
</div>
HTML;
} catch (Exception $e) {
    $error = $e->getMessage();

    echo <<<HTML
<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
    <div class="col-lg-6">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h5 text-gray-900 mb-2">Non è stato possibile completare la richiesta</h1>
                <p class="mb-4">$error</p>
                <hr />
                <p><a href="" onclick="window.history.back();">← Torna indietro</a></p>
            </div>
            <hr />
            <div class="text-center">
                <a class="small" href="register.php">Crea un Account!</a>
            </div>
            <div class="text-center">
                <a class="small" href="login.php">Hai già un account? Accesso!</a>
            </div>
        </div>
    </div>
</div>
HTML;
}
require_once ('components/parts/footer.php');
?>



