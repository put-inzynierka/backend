<?php declare(strict_types=1);

namespace App\Service\Mail;

use App\Entity\Team\TeamMember;

class TeamInvitationMailer
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly string $frontendUrl
    ) {
    }

    public function send(
        TeamMember $teamMember
    ): void {
        $this->mailer->send(
            $teamMember->getEmail(),
            'Team invitation',
            <<<MSG
                <p>Hi {$teamMember->getEmail()}!</p>
                <p>You have been invited to {$teamMember->getTeam()->getName()}.</p>
                <p>Please create your account here:</p>
                <p><a href="{$this->frontendUrl}/en/auth/sign-up">Sign up</a></p>      
                <p>or login if you have one already:</p>
                <p><a href="{$this->frontendUrl}/en/auth/sign-in">Sign in</a></p>
            MSG
        );
    }
}