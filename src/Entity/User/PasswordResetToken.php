<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table]
#[UniqueEntity(fields: 'value')]
class PasswordResetToken extends AbstractToken
{}
