<?php
include 'components/applications/User.php';
include 'components/applications/UserInfo.php';
include 'components/applications/database/SiteDAO.php';
include 'components/applications/SendMail.php';

class Session
{
    private $user;
    private $dao;

    public function __construct()
    {
        if( session_status() === PHP_SESSION_DISABLED  )
            header('HTTP/1.1 403 Forbidden');

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (isset($_SESSION['user']))
            $this->user = $_SESSION['user'];
        else
            $this->user = null;

        $this->dao = new SiteDAO();
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param null $user
     * @return Session
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUserData($email) {
        return $this->dao->getUserData($email);
    }

    public function login($email, $password) {
        try {
            if (!filter_var($email, FILTER_SANITIZE_EMAIL))
                throw new RuntimeException("Indirizzo e-mail non valido");
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                throw new RuntimeException("Indirizzo e-mail non valido");
            if (empty($password))
                throw new RuntimeException("La password inserita non è valida o il campo è stato lasciato vuoto.");

            $this->setUser($this->dao->loginUser($email, $password));

            $_SESSION['user'] = $this->getUserData($email);

            return true;

        } catch(Exception $e) {
            return $e->getMessage();
        }
    }

    public function logout() {
        try {
            if ($this->dao->logout())
                return true;
            else
                return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function register($userData) {
        // password check already done on register
        if (!filter_var($userData->getEmail(), FILTER_SANITIZE_EMAIL) || !(filter_var($userData->getEmail(), FILTER_VALIDATE_EMAIL)))
            throw new RuntimeException("Indirizzo e-mail inserito non valido.");
        if ($userData->getGender() != 0 && $userData->getGender() != 1 && $userData->getGender() != 2)
            throw new RuntimeException("Sesso non valido.");
        if ($userData->getPrivacy()!=1)
            throw new RuntimeException("Non hai acconsentito ai termini e condizioni.");

        try {
                $reg = $this->dao->register($userData);

                $hash = $reg['token'];
                $email = $reg['email'];

                $subject = 'SEPT - Conferma registrazione per la tesi di Gabriele Vaudano';

                include('components/parts/templates/email/intermediate.confirm.xml');
                $this->sendMail($userData->getEmail(), $subject, $message);

        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        return true;
    }

    private function sendMail($to, $subject, $message)
    {
        $mail = new SendMail($to, $subject, $message);

        try {
            $mail->send();

        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    private function sendAttackVector($to, $subject, $message)
    {
        $updateValue = $this->dao->updateAttack(1,0, $to);
        $to = $this->dao->decrypt($to);

        $mail = new SendMail($to, $subject, $message);

        try {
            if($mail->sendFake() && $updateValue)
                return true;
            else
                return false;
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function doFinalSurvey($email)
    {
        $subject = "SEPT - Sondaggio Finale Attivato";
        include('components/parts/templates/email/do-final-survey.xml');
        $this->sendFinalSurvey($email, $subject, $message);
    }

    private function sendFinalSurvey($to, $subject, $message)
    {
        $auth = $this->dao->updateAttack(1,1,$to);

        $to = $this->dao->decrypt($to);

        if ($auth)
            $mail = new SendMail($to, $subject, $message);

        try {
            $mail->send();

        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function validateRegister($auth, $token) {

        try {
            return $this->dao->validateRegister($auth, $token);
        } catch(Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function resendData($email) {
        $ut = $this->dao->getUserToken($email);

        $auth = $ut['user'];
        $hash = $ut['token'];

        $subject = 'SEPT - Conferma registrazione per la tesi di Gabriele Vaudano';

        include('components/parts/templates/email/resend-data.xml');

        $this->sendMail($email, $subject, $message);
        return true;
    }

    public function resendForgotData($email) {
        $ut = $this->dao->getUserToken($email);

        $auth = $ut['user'];
        $hash = $ut['token'];

        $subject = 'SEPT - Reimposta password dimenticata';
        include('components/parts/templates/email/forgot-password.xml');

        $this->sendMail($email, $subject, $message);
        return true;
    }

    public function emCompleted($email) {
        $email = $this->dao->decrypt($email);

        $subject = 'SEPT - Test completato';
        include('components/parts/templates/email/test-ended.xml');

        $this->sendMail($email, $subject, $message);
        return true;
    }

    public function changePassword($auth, $token, $new)
    {
        if ($this->dao->setNewPassword($auth, $new, $token))
            return true;
        else
            throw new RuntimeException("Non è stato possibile completare la procedura di cambio della password. Ritenta più tardi.");

    }

    public function isUserExists($email)
    {
        try {
            return $this->dao->isUserExist($email);
        } catch(Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getNotGroupedUsers()
    {
        return $this->dao->getNotGroupedUsers();
    }

    public function getGroupedUsers($type)
    {
        return $this->dao->getGroupedUsers($type);
    }

    public function changePermissions($email, $group)
    {

        if($this->dao->changePermissions($email, $group)) {

            $subject = 'SEPT - Account attivato. Ora puoi accedere all\'area personale\'!';
            include('components/parts/templates/email/final-confirm.xml');

            $email = $this->dao->decrypt($email);
            $this->sendMail($email, $subject, $message);
            return true;
        }

        return false;
    }

    public function startTest($email, $link) {
            $subject = 'SEPT - Contenuti informativi esclusivi via e-mail';
            include('components/parts/templates/email/test.xml');

            try {
                return $this->sendAttackVector($email, $subject, $message);
            } catch (Exception $e) {
                return false;
            }
    }

    public function getUsersData() {
        return $this->dao->getUsersData();
    }

    public function getAtkData($auth) {
        return $this->dao->getAtk($auth);
    }

    public function finalizeInTest($auth) {
        if($this->dao->updateAttack(1,9,$auth))
            return $this->emCompleted($auth);
    }

}