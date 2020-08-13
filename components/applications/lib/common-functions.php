<?php

if (!function_exists('getGroup')) {
    function getGroup($group, $active)
    {
        if ($active == 0)
            return "Account non attivato";
        else if ($active == 1 && $group == 0)
            return "<strong class='text-danger'>Gruppo non assegnato</strong>";
        else if ($active == 1 && $group == 1)
            return "Account attivato: Gruppo base";
        else if ($active == 1 && $group == 2)
            return "Account attivato: Gruppo con SEADMv2";
        else if ($active == 1 && $group == 9)
            return "Amministratore del sistema";
        else
            return "Non specificato";
    }
}
if (!function_exists('getAgeRange')) {
    function getAgeRange($age)
    {
        if ($age == 18)
            return "18-24";
        else if ($age == 25)
            return "25-34";
        else if ($age == 35)
            return "35-44";
        else if ($age == 45)
            return "45-54";
        else if ($age == 55)
            return "55-64";
        else if ($age == 65)
            return "65+";
        else
            return "ND";
    }
}
if (!function_exists('getGender')) {

    function getGender($gender)
    {
        if ($gender == 0)
            return "Maschio";
        else if ($gender == 1)
            return "Femmina";
        else
            return "Altro/Non specificato";
    }
}
if (!function_exists('getStatusAttack')) {
    function getStatusAttack($atkSent, $atkRes)
    {
        if ($atkSent == 0)
            return "Attacco non ancora avviato";
        else if ($atkSent == 1 && $atkRes == 0)
            return "Attacco completato, sondaggio finale ancora da somministrare";
        else if ($atkSent == 1 && $atkRes == 1)
            return "Attacco completato, sondaggio somministrato";
        else if ($atkSent == 1 && $atkRes == 9)
            return "Test completato";
        else
            return "Stato ignoto";
    }
}

if (!function_exists('getStatusAttackComplete')) {
    function getStatusAttackComplete($atkSent, $atkRes)
    {
        if ($atkSent == 0)
            return array("label" => "La registrazione è stata completata con successo ma il test non è stato ancora avviato. Ricordati di leggere le informazioni nella pagina principale.", "value" => 1);
        else if ($atkSent == 1 && $atkRes == 0)
            return array("label" => "Il test è stato avviato, ma non è ancora completato. Il sondaggio finale è ancora da somministrare.", "value" => 2);
        else if ($atkSent == 1 && $atkRes == 1)
            return array("label" => "La sperimentazione è stata completata, invece il sondaggio in corso di somministrazione", "value" => 3);
        else if ($atkSent == 1 && $atkRes == 9)
            return array("label" => "Tutte le parti del test sono state completate con successo. Ti ringrazio di cuore per la partecipazione.", "value" => 4);
        else
            return "Stato ignoto";
    }
}
