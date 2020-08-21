<?php
require_once ('components/applications/database/config/config.php');

define("DB_HOST", $host);
define("DB_DATABASE", $database);

define("DB_READ_USER", $usernameRO);
define("DB_READ_PASSWORD", $passwordRO);

define("DB_USER", $username);
define("DB_PASSWORD", $password);

class DbConnect
{
    private $mysqli;

    public function getMysqli()
    {
        return $this->mysqli;
    }

    function __construct($key) {
        if ($key=="read_only")
            $this->mysqli = new mysqli(DB_HOST, DB_READ_USER, DB_READ_PASSWORD, DB_DATABASE);
        else if ($key=="read_write")
            $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        else
            throw new RuntimeException("Connessione al database bloccata. Hai tentato un accesso illegale.");
        /* check connection */
        if (mysqli_connect_errno())
            throw new RuntimeException("La connessione è fallita: %s\n", mysqli_connect_error());

    }
    public function close(){
        $this->mysqli->close();
    }
}