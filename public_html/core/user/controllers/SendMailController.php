<?php

namespace core\user\controllers;

use core\base\exceptions\DbException;
use core\user\models\Model;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SendMailController extends BaseUser
{

    private string $_body = '';

    private string $_ErrorInfo = '';

    protected object $model;

    protected function inputData()
    {

        parent::inputData();

    }

    public function setMailBody(array|string $body): static
    {

        if (is_array($body)) {

            $body = implode("\n", $body);

        }

        $this->_body .= $body;

        return $this;

    }

    /**
     * @throws DbException
     */
    public function send(string $email, string $subject = ''): bool
    {

        if (!isset($this->model)) $this->model = Model::instance();

        $to = [];

        if (!$this->set) {

            $this->set = $this->model->read('settings', [
                'order' => ['id'],
                'limit' => 1
            ]);

            $this->set && $to[] = $this->set[0]['email'];

        }

        if ($email) {

            $to[] = $email;

        }

        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.yandex.ru';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'sycoqz@yandex.ru';                     //SMTP username
            $mail->Password   = 'wdifrcrbldivgoav';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('sycoqz@yandex.ru', 'Matchfixing soft inc. ' . $_SERVER['HTTP_HOST']);
//            $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
            foreach ($to as $address) {

                $mail->addAddress($address);

            }

            $mail->addReplyTo('sycoqz@yandex.ru');
//            $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');

            //Attachments
//            $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
//            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject ?: 'Matchfixing soft inc. ' . $_SERVER['HTTP_HOST'];
            $mail->Body    = $this->_body;
//            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();

            return true;

        } catch (Exception $e) {

            $this->_ErrorInfo = $mail->ErrorInfo;

        }

        return false;

    }

    public function getMailError(): string
    {

        return $this->_ErrorInfo;

    }

}