<?php

namespace App\Entity\Mail;

use App\Entity\AbstractEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table]
class MailLog extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $sender;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $recipients;

    #[ORM\Column(type: Types::STRING, length: 512)]
    private string $subject;

    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $sent;

    public function __construct(string $sender, array $recipients, string $subject, string $content)
    {
        $this->sender = $sender;
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->content = $content;
        $this->sent = false;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(string $sender): MailLog
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function setRecipients(array $recipients): MailLog
    {
        $this->recipients = $recipients;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): MailLog
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): MailLog
    {
        $this->content = $content;

        return $this;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): MailLog
    {
        $this->sent = $sent;

        return $this;
    }
}
