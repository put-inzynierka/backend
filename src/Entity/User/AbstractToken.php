<?php

namespace App\Entity\User;

use App\Entity\AbstractEntity;
use App\Entity\Component\Contract\Token;
use App\Enum\SerializationGroup\User\TokenGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Component\Validator\Constraints;

#[MappedSuperclass]
#[HasLifecycleCallbacks]
abstract class AbstractToken extends AbstractEntity implements Token
{
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $value;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Constraints\IsFalse(groups: [TokenGroups::INVOKE])]
    private bool $used;

    public function __construct(string $value, User $user)
    {
        $this->value = $value;
        $this->user = $user;
        $this->used = false;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): Token
    {
        $this->used = $used;

        return $this;
    }
}
