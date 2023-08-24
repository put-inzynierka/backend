<?php

namespace App\Component\Model;

use App\Enum\SerializationGroup\MessageGroups;
use Doctrine\DBAL\Types\Types;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

class Message
{
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false)]
    #[Groups([MessageGroups::CREATE])]
    #[Property(
        description: 'The title of the email',
        example: 'Przydział na RoboDay 2023',
    )]
    protected string $title;

    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1)]
    #[Constraints\NotBlank(allowNull: false)]
    #[Groups([MessageGroups::CREATE])]
    #[Property(
        description: 'The content of the email',
        example: 'Mycie toalet, proszę przynieść szczoteczkę i być na 4:00',
    )]
    protected string $message;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Message
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): Message
    {
        $this->message = $message;

        return $this;
    }
}
