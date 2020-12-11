<?php

namespace App\Utils;

use App\Utils\CustomMailer\EmailData;
use App\Utils\CustomMailer\MailLib;

class SendEmail
{
    public function __invoke(array $from, array $email_customer, string $subject, string $bodyText, string $bodyHtml, $attachments = [])
    {
        try {
            $emailData = new EmailData(
                (object) $from,
                $email_customer,
                $subject,
                $bodyText,
                $bodyHtml,
                $attachments,
                [],
                [
                    env('EMAIL_BCC_DEVELOPER'),
                ],
                (object) ['email' => env('EMAIL_REPLY_TO')]
            );

            $maillib = new MailLib([
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),
                'host' => env('MAIL_HOST'),
                'port' => env('MAIL_PORT'), ]);
            $maillib->Send($emailData);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
        }
    }
}
