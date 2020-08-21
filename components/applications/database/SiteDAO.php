<?php

require_once 'DbConnect.php';
include_once('components/applications/database/Base32.php');

use phpseclib\Crypt\AES;



class SiteDAO
{

    private $db;
    private $key = "p7H7FMDPkr3FfARMJKK42UrjxpCdVNk8";

    function __construct() {
        // connecting to database
    }

    private function encryptData($data) {
        return Base32::encode($data);
    }

    public function decrypt($data)
    {
        $ch = Base32::decode($data);
        return $ch;
    }

    private function getToken($length){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max-1)];
        }

        return hash('sha256',$token);
    }

    public function getUserData($email) {
        $this->db = new DbConnect("read_only");

        if ( $stmt = $this->db->getMysqli()->prepare("SELECT gender, `level`, age, privacy FROM `user-informations` WHERE email = ? ")) {
            $email_hashed = $this->encryptData($email);

            $stmt->bind_param("s", $email_hashed);
            $stmt->execute();

            $stmt->bind_result( $gender, $level, $age, $privacy);
            $stmt->fetch();


            $userData = new UserInfo($email, null, null, $age, $gender, $level, $privacy);
            $stmt->close();
        }

        $stmt = $this->db->getMysqli()->prepare("SELECT `group` FROM `users` WHERE email = ? ");

        if ($stmt) {
            $stmt->bind_param("s", $email_hashed);
            $stmt->execute();

            $stmt->bind_result($group);
            $stmt->fetch();

            if(isset($userData))
                $userData->setGroup($group);

            $stmt->close();
            $this->db->close();
        }

        if (isset($userData))
            return $userData;

        throw new RuntimeException("Ci sono stati problemi con il login. Non è stato possibile completare la richiesta, ritenta più tardi.");
    }

    // destructor
    function __destruct() {
    }

    public function loginUser($email, $password){
        $this->db = new DbConnect("read_only");

        $email_hashed = $this->encryptData($email);
        $stmt = $this->db->getMysqli()->prepare("SELECT COUNT(email), password, `group`, active FROM users WHERE email = ? GROUP BY EMAIL ");

        if ($stmt) {

            $stmt->bind_param("s", $email_hashed);
            $stmt->execute();

            $stmt->bind_result($num, $db_password, $group, $active);
            $stmt->fetch();

            $stmt->close();
            $this->db->close();

            if ($num==0)
                throw new RuntimeException("Nome utente o password errate. L'utente non è stato trovato.");
            else if ($num == 1)
            {
                // password verify
                if (!password_verify($password, $db_password))
                    throw new RuntimeException("Nome utente o password non valida. Ti consigliamo di provare a immettere nuovamente le tue credenziali.");
                // verified account verify
                if ($active == 0)
                    throw new RuntimeException("<p>L'account non è ancora stato attivato. Clicca sul link di conferma inviato via e-mail.</p><p>Per richiedere una ulteriore e-mail di conferma, </p><form action=\"register.php\" method=\"post\">
    <input type=\"hidden\" name=\"resend-verification-link\" value=\"1\" />
    <input type='hidden' name='email' value='$email' />
    <input type='submit' class='btn btn-primary' value='clicca qui'>
</form></p>");
                // group appartency verify
                if ($group == 0)
                    throw new RuntimeException("<p><strong>Riceverai una e-mail appena l'account verr&agrave; attivato!</strong></p><p>Non sei ancora stato assegnato ad un gruppo per il test. Finchè Gabriele Vaudano non controllerà la tua richiesta e ti collocherà in un gruppo, non potrai accedere al servizio.");

                $_SESSION['uid'] = $this->getAnonymousString($email);
                // all ok -> return user
                return new User($email, null, $group);
            }
        }

        throw new RuntimeException("Ci sono stati problemi con il login. Non è stato possibile completare la richiesta, ritenta più tardi.");
    }

    public function logout()
    {
        if (isset($_SESSION['user'])) {
            $_SESSION = array();
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
            session_destroy();
            return true;
        }
        else
            return false;
    }

    public function isUserExist($email){
         $this->db = new DbConnect("read_only");

        try {
            if ($stmt = $this->db->getMysqli()->prepare("SELECT COUNT(email) FROM users WHERE email = ? GROUP BY email")) {
                $email_hashed = $this->encryptData($email);

                $stmt->bind_param("s", $email_hashed);
                $stmt->execute();
                $stmt->bind_result($count);

                $stmt->fetch();

                $stmt->close();
                $this->db->close();

                if ($count == 1)
                    return true;
                else
                    return false;
            }

        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
        return false;
    }

     public function getUserToken($email){
        $this->db = new DbConnect("read_only");
         $email_hashed = $this->encryptData($email);

         try {
            if ($stmt = $this->db->getMysqli()->prepare("SELECT hash FROM users WHERE email = ? ")) {
                $stmt->bind_param("s", $email_hashed);
                $stmt->execute();
                $stmt->bind_result($hash);

                $stmt->fetch();

                $stmt->close();
                $this->db->close();
            }

            if(isset($hash))
                return array("user"=>$email_hashed,"token"=>$hash);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

     }
    private function registerUser($userData, $hash)
    {
        $email = $userData->getEmail();
        $email_hashed = $this->encryptData($userData->getEmail());

        try {
            if ($this->isUserExist($email))
                throw new RuntimeException("L'utente è già registrato con tale indirizzo e-mail.");

            $this->db = new DbConnect("read_write");

            /**  USER DATA TO VARIABLE FOR REFERENCE */
                $password= $userData->getPassword();
                $group = 0;
                $gender = $userData->getGender();
                $level = $userData->getTechLevel();
                $age = $userData->getRangeAge();
                $privacy = 1;
                $active = 0;
            /** END OF USER DATA FOR REFERENCE */

            /* START TRANSACTION */
            $this->db->getMysqli()->query("BEGIN;");

            $stmt = $this->db->getMysqli()->prepare("INSERT INTO `users` (email, password, `group`, active, hash) VALUES (?, ?, ?, ?, ?); ");

            if(!$stmt->bind_param("ssiis", $email_hashed, $password, $group, $active, $hash)) {
                $this->db->close();
                throw new RuntimeException("Non è stato possibile completare la procedura di registrazione. Ritenta tra un paio di minuti.");
            }
            $stmt->execute();

            $stmt->close();

            $stmt = $this->db->getMysqli()->prepare("INSERT INTO `user-informations` (email, gender, `level`, age, privacy) VALUES (?, ?, ?, ?, ?);");
            $stmt->bind_param("siiii", $email_hashed, $gender, $level, $age, $privacy);
            $stmt->execute();

            if (!$this->db->getMysqli()->query("COMMIT;"))
                throw new RuntimeException("Ci sono stati problemi con l'inserimento del nuovo utente. Ti preghiamo di riprovare o contattare l'amministratore del sistema.");
            $stmt->close();
            $this->db->close();
            /* END TRANSACTION */
            return true;
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function register($userData) {
        try {
            $token = $this->getToken(16);

            if ($this->registerUser($userData, $token))
                return array("email"=>$this->encryptData($userData->getEmail()), "token"=>$token);

            throw new RuntimeException("La registrazione non è andata a buon fine. Ritenta la registrazione o contatta l'amministratore del sistema.");
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function validateRegister($auth, $token)
    {
        $this->db = new DbConnect("read_write");

        try {
            $stmt = $this->db->getMysqli()->prepare("SELECT COUNT(*) FROM `users` WHERE hash = ? AND email = ? ");
            $stmt->bind_param("ss", $token, $auth);
            $stmt->execute();

            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count==0) {
                $this->db->close();
                throw new RuntimeException("L'account che stai cercando di verificare non esiste o il token di verifica è scaduto.");
            }

            $stmt = $this->db->getMysqli()->prepare("SELECT active FROM `users`  WHERE hash = ? AND email = ? ");
            $stmt->bind_param("ss", $token, $auth);
            $stmt->execute();

            $stmt->bind_result($active);
            $stmt->fetch();
            $stmt->close();

            if ($active!=0) {
                $this->db->close();
                throw new RuntimeException("Hai già verificato l'account. <p>Torna al <a href='login.php'>menu principale</a>.");
            }

            $stmt = $this->db->getMysqli()->prepare("UPDATE `users` SET active = 1 WHERE hash = ? AND email = ? ");
            $stmt->bind_param("ss", $token, $auth);
            $stmt->execute();

            $stmt->close();

            $newHash = $this->getToken(16);

            $stmt = $this->db->getMysqli()->prepare("UPDATE `users` SET hash = ? WHERE  email = ? ");
            $stmt->bind_param("ss", $newHash, $auth);
            $stmt->execute();

            $stmt->close();
            $this->db->close();

            return true;

        }  catch(RuntimeException $rte) {
            throw new RuntimeException($rte->getMessage());
        } catch (Exception $e) {
            throw new RuntimeException("Non è stato possibile completare la procedura di verifica della registrazione. Ti consigliamo di contattare l'assistenza o direttamente <a href='mailto:gabriele.vaudano@studenti.polito.it''>Gabriele Vaudano</a>.");
        }
    }


    public function setNewPassword($email, $new, $hash)
    {
        $this->db = new DbConnect("read_only");

        try {
            $stmt = $this->db->getMysqli()->prepare("SELECT active FROM `users` WHERE hash = ? AND email = ? ");
            $stmt->bind_param("ss", $hash, $email);
            $stmt->execute();

            $stmt->bind_result($active);
            $stmt->fetch();
            $stmt->close();

            $token = $this->getUserToken($this->decrypt($email))['token'];

            if($hash!=$token)
                throw new ArithmeticError("Non è stato possibile completare la procedura di cambio della password. Riprova più tardi.");
            if (!($active==1)) {
                $this->db->close();
                throw new RuntimeException("L'account non è ancora stato attivato. Prima di richiedere il cambio della password attiva l'account.");
            }

            $newHash = $this->getToken(16);

            $this->db = new DbConnect("read_write");

            if($stmt = $this->db->getMysqli()->prepare("UPDATE `users` SET password = ?, hash = ? WHERE email = ? AND hash = ? "))
                throw new RuntimeException("Non è stato possibile completare la procedura di cambio della password. Riprova più tardi.");

            if(!$stmt->bind_param("ssss", $new,$newHash, $email, $hash))
                throw new RuntimeException("Non è stato possibile completare la procedura di cambio della password. Riprova più tardi.");

            if(!$stmt->execute())
                throw new RuntimeException("Non è stato possibile completare la procedura di cambio della password. Riprova più tardi.");

            $stmt->close();
            $this->db->close();

            return true;

        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getNotGroupedUsers()
    {
        include_once ('components/applications/lib/common-functions.php');
        $this->db = new DbConnect("read_only");

        try {
            if($stmt = $this->db->getMysqli()->prepare("SELECT `users`.email, age, `level`, gender FROM `user-informations`, `users` WHERE `users`.email = `user-informations`.email AND `group` = 0 ")) {
                $stmt->execute();

                $stmt->bind_result($data, $age, $level, $gender);
                $output = "";

                while ($stmt->fetch()) {
                    $output .= "<option value='" . $data . "'>" . $this->getAnonymousString($data) . " (sesso: " . getGender($gender) . ", età: " . getAgeRange($age) . ", competenze digitali: " . $level . ")</option>";
                }

                $stmt->close();
                $this->db->close();
                return $output;
            } else
                throw new RuntimeException("Problemi di connessione al database.");

        } catch (Exception $e) {
            return $e->getMessage();
        }

    }
    private function getAnonymousString($string)  {
        $sum = 0;
        for($i = 0;  $i < strlen($string); $i++)
            $sum += ord($string[$i]);

        return base64_encode($sum);
    }

    public function changePermissions($email, $group)
    {
        $this->db = new DbConnect("read_write");

        if ($stmt = $this->db->getMysqli()->prepare("UPDATE `users` SET `group` = ? WHERE email = ? ")) {
            $stmt->bind_param("ss", $group, $email);
            $stmt->execute();

            $stmt->close();
            $this->db->close();

            echo "<script>alert('Cambio permessi avvenuto con successo.')</script>";
            return true;
        }
    }

    public function getGroupedUsers($type)
    {
        include_once ('components/applications/lib/common-functions.php');

        $this->db = new DbConnect("read_only");

        try {
            if($stmt = $this->db->getMysqli()->prepare("SELECT `users`.email, age, `level`, gender, `attack-sent`, `attack-result` FROM `user-informations`, `users` WHERE `users`.email = `user-informations`.email AND NOT `group` = 0 AND NOT `active` = 0")) {
                $stmt->execute();

                $stmt->bind_result($data, $age, $level, $gender, $atkSt, $atkRece);
                $output = "";

                while ($stmt->fetch()) {
                    var_dump($atkSt);
                    $atkSent = $atkSt;
                    if ($type==1) { // Attiva attacco
                        if ($atkSent == 0)
                            $output .= "<option value='" . $data . "'>" . $this->getAnonymousString($data) . " (sesso: " . getGender($gender) . ", età: " . getAgeRange($age) . ", competenze digitali: " . $level . ")</option>";
                    }
                    else if ($type == 2) {// Attiva sondaggio finale
                        if ($atkSent == 1 && $atkRece == 0)
                            $output .= "<option value='" . $data . "'>" . $this->getAnonymousString($data) . " (sesso: " . getGender($gender) . ", età: " . getAgeRange($age) . ", competenze digitali: " . $level . ")</option>";
                    }
                    else if ($type==3) {
                        if ($atkRece == 1)
                            $output .= "<option value='" . $data . "'>" . $this->getAnonymousString($data) . " (sesso: " . getGender($gender) . ", età: " . getAgeRange($age) . ", competenze digitali: " . $level . ")</option>";
                    }
                }

                $stmt->close();
                $this->db->close();
                return $output;
            } else
                throw new RuntimeException("Problemi di connessione al database.");

        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

    public function getUsersData() {

        $this->db = new DbConnect("read_only");

        try {
            if ($stmt = $this->db->getMysqli()->prepare("SELECT `users`.`email`,`gender`,`level`,`age`,`attack-sent`,`attack-result`, `group`, `active` FROM `user-informations`, `users` WHERE `user-informations`.email = `users`.email  ")) {
                $stmt->execute();

                $stmt->bind_result($email, $gender, $level, $age, $atkSent, $atkRes, $group, $active);
                $output = "";

                while ($stmt->fetch()) {
                    $status = getStatusAttack($atkSent, $atkRes);
                    $email = $this->getAnonymousString($email);
                    $output .= "<tr><td>$email</td> <td>" . getGroup($group, $active) . "</td><td>" . getGender($gender) . "</td><td>" . getAgeRange($age) . "</td><td>$level/5</td><td>$status</td></tr>";
                }

                $stmt->close();
                $this->db->close();
                return $output;
            } else
                throw new RuntimeException("Problemi di connessione al database.");

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateAttack($atke, $atkr, $auth)
    {
        $this->db = new DbConnect("read_write");

        if ($stmt = $this->db->getMysqli()->prepare("UPDATE `user-informations` SET `attack-sent` = ? , `attack-result` = ? WHERE email = ? ")) {
            $stmt->bind_param("iis", $atke, $atkr, $auth);
            $stmt->execute();

            $stmt->close();
            $this->db->close();

            return true;
        }

        return false;
    }

    public function getAtk($auth)
    {
        $auth = $this->encryptData($auth);

        $this->db = new DbConnect("read_only");

        try {
            if ($stmt = $this->db->getMysqli()->prepare("SELECT `attack-sent`, `attack-result` FROM `user-informations`, `users` WHERE `users`.email = `user-informations`.email AND `users`.email = ? ")) {
                $stmt->bind_param("s", $auth);
                $stmt->execute();

                $stmt->bind_result($atkSt, $atkRece);
                $output = "";

                $stmt->fetch();


                $stmt->close();
                $this->db->close();
                return array("atkSent" => $atkSt, "AtkRe" => $atkRece);
            }
        } catch (Exception $e) {
            throw new RuntimeException("Problemi di connessione al database.");
        }
    }
}
?>
