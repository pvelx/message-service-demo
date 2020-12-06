<?php declare(strict_types=1);


namespace App\Contract\Mailer;


interface MailerInterface
{
    /**
     * @param string $recipient
     * @param string $text
     * @return bool
     * @throws MailerExceptionInterface
     */
    public function send(string $recipient, string $text);
}
