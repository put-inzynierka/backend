<?php

namespace App\Service\Mail;

use App\Entity\Component\Contract\Token;
use App\Entity\User\User;

class PasswordResetMailer
{
    public function __construct(
        protected Mailer $mailer,
        protected string $urlFormat,
        protected string $locale
    ) {}

    public function send(
        User $recipient,
        Token $resetToken
    ): void {
        $url = sprintf($this->urlFormat, $this->locale, $resetToken->getValue());

        $this->mailer->send(
            $recipient->getEmail(),
            'Password reset',
            <<<MSG
                <p>Hi {$recipient->getFirstName()}!</p>
                <p>You have requested a password reset. Please click the link below to reset your password.</p>
                <p><a href="$url">Reset password</a></p>
                <p>If you did not request a password reset, please ignore this email.</p>
            MSG
        );
    }
}
