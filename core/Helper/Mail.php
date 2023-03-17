<?php

namespace Core\Helper;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/**
 * @author Delamare Paul
 */
class Mail{
    private string $host= 'localhost';
    private bool $smtp= false;
    private int $port = 1025;
    private string $charset= 'UTF-8';
    private string $setFrom= 'cvcheck@gmail.com';
    private bool $isHTML= true;

    protected function sendMail($content, $email, $subject='Envoi Mail', $mailFrom = false){
        if (!empty($content)){
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = $this->host;
                $mail->SMTPAuth = $this->smtp;
                $mail->Port = $this->port;
                $mail->CharSet=$this->charset;

                if ($mailFrom){
                    $mail->setFrom($email);
                    $mail->addAddress($this->setFrom);
                }else{
                    $mail->setFrom($this->setFrom);
                    $mail->addAddress($email);
                }

                $mail->isHTML($this->isHTML);
                $mail->Subject = $subject;
                $mail->Body = $content;

                $mail->send();
            } catch (Exception $e) {
                echo $e;
            }
        }
    }
    protected function contentAssembly($template, $messages=[]){
        ob_start();
        include($this->nameTemplate('app.email.layout.header-mail'));
        if ((count($messages)>0)){
            extract($messages);
        }
        include $this->nameTemplate($template);

        include($this->nameTemplate('app.email.layout.footer-mail'));
        $message = ob_get_clean();
        return $message;
    }
    protected function nameTemplate(string $viewer)
    {
        return $this->getViewPathEmail().str_replace('.','/',$viewer).'.php';
    }
    private function getViewPathEmail() : string
    {
        return __DIR__ . '/../../template/';
    }
}
