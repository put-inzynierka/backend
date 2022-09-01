<?php

namespace App\Entity\Game;

use App\Entity\AbstractEntity;
use App\Enum\SerializationGroup\Game\KeyGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
#[UniqueEntity(fields: 'value')]
class Key extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:14, max: 29)]
    #[Constraints\Regex(pattern: '/^([A-Za-z0-9]{4,5}-){2,}[A-Za-z0-9]{4,5}$/')]
    #[Constraints\NotBlank(allowNull: false, groups: [KeyGroups::CREATE])]
    #[Groups([
        KeyGroups::CREATE,
        KeyGroups::SHOW,
        KeyGroups::INDEX,
        KeyGroups::UPDATE,
    ])]
    private string $value;

    #[ORM\Column(type: Types::BOOLEAN, length: 255)]
    #[Groups([
        KeyGroups::SHOW,
        KeyGroups::INDEX,
    ])]
    private bool $redeemed = false;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn]
    #[Groups([
        KeyGroups::SHOW,
        KeyGroups::INDEX,
    ])]
    private Game $game;

    #[ORM\ManyToOne(targetEntity: Platform::class)]
    #[ORM\JoinColumn]
    #[Groups([
        KeyGroups::SHOW,
        KeyGroups::INDEX,
    ])]
    private Platform $platform;

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function isRedeemed(): bool
    {
        return $this->redeemed;
    }

    public function setRedeemed(bool $redeemed): self
    {
        $this->redeemed = $redeemed;

        return $this;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setGame(Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getPlatform(): Platform
    {
        return $this->platform;
    }

    public function setPlatform(Platform $platform): self
    {
        $this->platform = $platform;

        return $this;
    }
}
