<?php

namespace App\Service\Mail;

use App\Entity\Mail\MailLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface as BaseMailer;

class Mailer
{
    public function __construct(
        protected string $sender,
        protected string $senderName,
        protected EntityManagerInterface $entityManager,
        protected BaseMailer $baseMailer
    ) {}

    public function send(
        string $to,
        string $subject,
        string $content,
        ?string $from = null,
        ?string $fromName = null
    ): MailLog {
        $from = $from ?? $this->sender;
        $fromName = $fromName ?? $this->senderName;

        $mailLog = new MailLog(
            $from,
            [$to],
            $subject,
            $content
        );

        $this->entityManager->persist($mailLog);
        $this->entityManager->flush();

        $mail = new Email();
        $mail
            ->from(new Address($from, $fromName))
            ->to($to)
            ->subject($subject)
            ->html($content)
        ;

        $this->baseMailer->send($mail);

        $mailLog->setSent(true);
        $this->entityManager->flush();

        return $mailLog;
    }
}
