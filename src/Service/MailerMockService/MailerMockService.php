<?php declare(strict_types=1);


namespace App\Service\MailerMockService;


use App\Contract\Mailer\MailerExceptionInterface;
use App\Contract\Mailer\MailerInterface;

class MailerMockService implements MailerInterface
{
    /**
     * @param string $recipient
     * @param string $text
     * @throws MailerExceptionInterface
     */
    public function send(string $recipient, string $text)
    {
        //TODO make me
    }
}
