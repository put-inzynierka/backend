<?php

namespace App\Service\Mail;

use App\Entity\Component\Contract\Token;
use App\Entity\User\User;

class RegistrationMailer
{
    public function __construct(
        protected Mailer $mailer,
        protected string $urlFormat,
        protected string $locale
    ) {}

    public function send(
        User $recipient,
        Token $activationToken
    ): void {
        $url = sprintf($this->urlFormat, $this->locale, $activationToken->getValue());

        $this->mailer->send(
            $recipient->getEmail(),
            'Registration',
            <<<MSG
                <p>Hi {$recipient->getFirstName()}!</p>
                <p>Thank you for registering.</p>
                <p>Please click the link below to activate your account:</p>
                <p><a href="$url">Activate</a></p>
            MSG
        );
    }
}
