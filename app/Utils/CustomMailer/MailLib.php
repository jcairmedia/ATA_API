<?php

namespace App\Utils\CustomMailer;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * MailLib - Wrapper para envío de correo electrónico usando PHPMailer.
 *
 * @author Miguel Vázquez <miguel.vazquez@tanaholdings.com>
 *
 * @version 1.0.1
 */
class MailLib
{
    private $config = [];

    /**
     * Constructor.
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Send.
     *
     * Envío de correo dado los valores asignados a la instancia por parámetro
     *
     * @param EmailData $emailData Encapsulamiento de parámetros
     */
    public function Send(EmailData $emailData)
    {
        $mail = new PHPMailer(true);
        // $mail->Username = $this->config['username'];
        // $mail->Password = $this->config['password'];
        //$phpmail->SMTPSecure = 'ssl';
        $mail->SMTPAutoTLS = false;
        // $mail->Host = $this->config['host'];
        // $mail->Port = $this->config['port'];
        $mail->IsSMTP();
        // $mail->SMTPAuth = true;

        try {
            //Server settings
         $mail->SMTPDebug = 0;                                 // Enable verbose debug output
         $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->isHTML(false);

            //From
            if (property_exists($emailData->getFrom(), 'name')) {
                $mail->setFrom($emailData->getFrom()->email, $emailData->getFrom()->name);
            } else {
                $mail->setFrom($emailData->getFrom()->email, $emailData->getFrom()->email);
            }

            //Recipients
            foreach ($emailData->getTO() as $k => $v) {
                if (!isset($v) || $v == '') {
                    continue;
                }

                if (is_int($k)) {
                    // Name is optional
                    $mail->addAddress($v);
                } elseif (is_string($k) && $k != '') {
                    // Add a recipient
                    $mail->addAddress($v, $k);
                }
            }

            // Reply To
            if ($emailData->getReplyTo() != null) {
                $mail->addReplyTo($emailData->getReplyTo()->email, $emailData->getReplyTo()->email);
            }

            // CC
            $arrayCC = $emailData->getCC();
            foreach ($arrayCC as $k => $v) {
                if (!isset($v) || $v == '') {
                    continue;
                }

                if (is_int($k)) {
                    // Name is optional
                    $mail->addCC($v);
                } elseif (is_string($k) && $k != '') {
                    // Add a recipient
                    $mail->addCC($v, $k);
                }
            }

            // BCC
            $arrayBCC = $emailData->getBCC();
            foreach ($arrayBCC as $k => $v) {
                if (!isset($v) || $v == '') {
                    continue;
                }

                if (is_int($k)) {
                    // Name is optional
                    $mail->addBCC($v);
                } elseif (is_string($k) && $k != '') {
                    // Add a recipient
                    $mail->addBCC($v, $k);
                }
            }

            // Attachments
            $arrayAttachments = $emailData->getAttachments();
            foreach ($arrayAttachments as $k => $v) {
                if (!isset($v) || $v == '') {
                    continue;
                }
                if (!file_exists($v)) {
                    continue;
                }

                if (is_int($k)) {
                    // Add attachment
                    $mail->addAttachment($v);
                } elseif (is_string($k) && $k != '') {
                    // Add attachment with a alias name
                    $mail->addAttachment($v, $k);
                }
            }

            $mail->Subject = $emailData->getSubject();

            if ($emailData->getHtmlBody() != null) {
                $mail->isHTML(true);
                // Send Alternative Body
                $mail->AltBody = $emailData->getPlainBody();
                // Set email format to HTML
                $mail->Body = $emailData->getHtmlBody();
            } else {
                $mail->Body = $emailData->getPlainBody();
            }

            // Send
            $b = $mail->send();

            if ($b === false) {
                \Log::error($mail->ErrorInfo);
                throw new Exception($mail->ErrorInfo);
            }

            return $b;
        } catch (Exception $ex) {
            $error = (string) $mail->ErrorInfo;
            \Log::error($error);

            throw new Exception($ex->getMessage().($error != '' ? ' | '.$error : ''), (int) $ex->getCode(), $ex);
        }
    }
}
