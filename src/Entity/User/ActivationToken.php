<?php

namespace App\Entity\User;

use App\Entity\AbstractEntity;
use App\Entity\Component\Contract\Token;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table]
#[UniqueEntity(fields: 'value')]
class ActivationToken extends AbstractEntity implements Token
{
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $value;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: Types::BOOLEAN)]
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

    public function setValue(string $value): ActivationToken
    {
        $this->value = $value;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): ActivationToken
    {
        $this->user = $user;

        return $this;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): ActivationToken
    {
        $this->used = $used;

        return $this;
    }
}
