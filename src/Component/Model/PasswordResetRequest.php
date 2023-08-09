<?php

namespace App\Component\Model;

use App\Enum\SerializationGroup\User\TokenGroups;
use Doctrine\DBAL\Types\Types;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

class PasswordResetRequest
{
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false)]
    #[Constraints\Email]
    #[Groups([TokenGroups::CREATE])]
    #[Property(
        description: 'The email to send the password reset link to',
        example: 'test@gmail.com',
    )]
    protected string $email;

    public function getEmail(): string
    {
        return $this->email;
    }
}
