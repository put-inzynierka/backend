<?php

namespace App\Entity\User;

use App\Entity\AbstractEntity;
use App\Enum\SerializationGroup\User\UserGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\UserEntityInterface;
use OpenApi\Attributes\Property;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
#[UniqueEntity(fields: 'email')]
class User extends AbstractEntity implements PasswordAuthenticatedUserInterface, UserEntityInterface, UserInterface
{
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [UserGroups::CREATE])]
    #[Constraints\Email]
    #[Groups([
        UserGroups::CREATE,
        UserGroups::SHOW,
        UserGroups::INDEX,
        UserGroups::UPDATE,
    ])]
    #[Property(
        description: 'The email of the user',
        example: 'test@gmail.com',
    )]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [UserGroups::CREATE])]
    #[Groups([
        UserGroups::CREATE,
        UserGroups::SHOW,
        UserGroups::INDEX,
        UserGroups::UPDATE,
    ])]
    #[Property(
        description: 'First name of the user',
        example: 'Mariusz',
    )]
    private string $firstName;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [UserGroups::CREATE])]
    #[Groups([
        UserGroups::CREATE,
        UserGroups::SHOW,
        UserGroups::INDEX,
        UserGroups::UPDATE,
    ])]
    #[Property(
        description: 'Last name of the user',
        example: 'Pudzianowski',
    )]
    private string $lastName;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [UserGroups::CREATE])]
    #[Groups([
        UserGroups::CREATE,
        UserGroups::UPDATE,
    ])]
    #[Property(
        description: 'The password of the user',
        example: 'password1',
    )]
    private string $password;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getIdentifier();
    }
}
