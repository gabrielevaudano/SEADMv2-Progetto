<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('vendor/autoload.php');


class SendMail {
    private $to;
    private $subject;
    private $message;
    private $mail;

    /**
     * SendMail constructor.
     * @param $to
     * @param $subject
     * @param $message
     */
    public function __construct($to, $subject, $message)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
        $this->mail = new PHPMailer(false);
    }


    public function send()
    {
        require_once('components/applications/database/config/mail.settings.php');

        try {
            $mail = $this->mail;

            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->IsHTML(true);
            $mail->Host = $emailHost;
            $mail->SMTPAuth = true;
            $mail->Username = $emailUser;
            $mail->Password = $emailPassword;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom($emailFrom, $emailLabelFrom);
            $mail->addAddress($this->to, $this->to);

            //Content
            $mail->isHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body = $this->message;

            $mail->send();
        } catch (Exception $e) {
            throw new RuntimeException($mail->ErrorInfo);
        }
    }

    public function sendFake()
    {
        require_once('components/applications/database/config/mail.settings.php');

        try {
            $mail = $this->mail;

            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->IsHTML(true);
            $mail->Host = $emailFakeHost;
            $mail->SMTPAuth = true;
            $mail->Username = $emailFakeUsername;
            $mail->Password = $emailFakePassword;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom($emailFakeFrom, $emailFakeFromLabel);
            $mail->addAddress($this->to, $this->to);

            //Content
            $mail->isHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body = $this->message;

            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new RuntimeException($mail->ErrorInfo);
        }
    }
}
