<?php
$public =  false;
$session = false;
require('components/parts/header.php');
include ('components/parts/site/index-top.xml');

if ($session->getAtkData($_SESSION['user']->getEmail())['atkSent'] == 1 && $session->getAtkData($_SESSION['user']->getEmail())['AtkRe'] == 1) {
    include('components/parts/site/index-final-survey.xml');
}
else if ($session->getAtkData($_SESSION['user']->getEmail())['atkSent'] == 1 && $session->getAtkData($_SESSION['user']->getEmail())['AtkRe'] == 9) {
    include('components/parts/site/index-test-completed.xml');
}
else if ($session->getAtkData($_SESSION['user']->getEmail())['atkSent'] == 1 && $session->getAtkData($_SESSION['user']->getEmail())['AtkRe'] == 0) {
    include ('components/parts/site/index-atk-sent.xml');
}

if($_SESSION['user']->getGroup()==2) {
    $title = "Consultazione delle fonti e utilizzo del modello SEADMv2";
    $body = "<p>Nei prossimi giorni, ogni volta che riceverai un'email sospetta dovrai accedere a questo portale, aprire lo strumento <a href=\"seadmv2.php\" target=\"_blank\">\"SEADMv2\"</a> dal menu laterale e utilizzarlo. 
                                        SEADNMv2 ti consiglia se completare le richieste (come aprire un sito web, immettere dati sensibili, fornire coordinate bancarie) pervenute da e-mail potenzialmente malevole,  in base ad una serie di domande a cui rispondere sì o no.</p>
                                    <p class=\"card card-text text-primary border-left-primary pl-4 p-3\"><span class='text-primary font-weight-bold'>Cos'è SEADMv2?</span> Maggiori informazioni nella pagina SEADMv2 dal menu laterale o nella sezione sottostante.</p>
                                    <p class=\"card card-text bg-gradient-danger p-4 text-light\"><span class=\"text-white font-weight-bold\">Dove trovare SEADMv2?</span><span>- Apri il browser (come Google Chrome), cerca <a href='https://sept.tech' class='text-warning'>https://sept.tech</a>, effettua il login e, una volta completato l'accesso, dal menu laterale seleziona: Strumenti di prevenzione > SEADMv2;</span> <span>- Accesso diretto all'applicazione: <u>https://sept.tech/tool.external.php</u> (consiglio: salvalo nei preferiti o segnalo!).</span></p>

                                    <p><strong>Richieste aggiuntive al consumatore del test:</strong> Ti consiglio di consultare il 'Materiale Informativo' accessibile dal menu laterale nel caso in cui non conoscessi il fenomeno dell'ingegneria sociale e attacchi come phishing, baiting.</p>
                                    ";
    $toolData = <<<HTML
<p>Le funzionalità ad ora implementate sono:</p>
                        <ul>
                            <li>Compendio di Ingegneria sociale: una guida online facilmente consultabile per scoprire le potenzialità dell'ingegneria sociale, vedere qualche esempio pratico e provare le tecniche di difesa. L'informazione è la migliore arma contro questa tipologia di attacchi;</li>
                            <li>SEADMv2: uno strumento a risposta chiusa (Sì/No) da utilizzare quando si sospetta di stare per cadere vittima di un attacco; esso consiglia che azioni intraprendere nei confronti di una richiesta potenzialmente malevola. Andrebbe sempre usato in contesti rischiosi, come e-mail da utenti estranei o cui l'identità non puà essere verificata o in ambienti lavorativi altamente vulernabili;</li>
                        </ul>
                        <hr>
                        <p><a href="seadmv2.php">Utilizza il modello di prevenzione SEADMv2 &rarr;</a></p>
HTML;

}
else {
    $title = "Consultazione delle fonti";
    $body = "<p><strong>Consigli di lettura per affrontare il test:</strong> Ti consiglio di consultare il 'Materiale Informativo' accessibile dal menu laterale nel caso in cui non conoscessi il fenomeno dell'ingegneria sociale e attacchi come phishing, baiting.</p>";
    $toolData = "";
}

include ('components/parts/site/index-main.xml');
include ('components/parts/site/index-how-tools-work.xml');

require_once ('components/parts/footer.php');
?>
