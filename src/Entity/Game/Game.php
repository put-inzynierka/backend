<?php

namespace App\Entity\Game;

use App\Entity\AbstractEntity;
use App\Entity\User\User;
use App\Enum\SerializationGroup\Game\GameGroups;
use App\Enum\SerializationGroup\Game\KeyGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
#[UniqueEntity(fields: 'steamId')]
class Game extends AbstractEntity
{
    #[ORM\Column(type: Types::INTEGER, unique: true)]
    #[Constraints\Type(type: Types::INTEGER)]
    #[Constraints\Length(min:1)]
    #[Constraints\NotBlank(allowNull: false, groups: [GameGroups::CREATE])]
    #[Groups([
        GameGroups::CREATE,
        GameGroups::SHOW,
        GameGroups::INDEX,
        GameGroups::UPDATE,
    ])]
    private int $steamId;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn]
    private User $owner;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [GameGroups::CREATE])]
    #[Groups([
        GameGroups::CREATE,
        GameGroups::SHOW,
        GameGroups::INDEX,
        GameGroups::UPDATE,
        KeyGroups::INDEX,
        KeyGroups::SHOW,
    ])]
    private string $name;

    public function getSteamId(): int
    {
        return $this->steamId;
    }

    public function setSteamId(int $id): self
    {
        $this->steamId = $id;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
