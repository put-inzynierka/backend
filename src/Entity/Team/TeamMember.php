<?php declare(strict_types=1);

namespace App\Entity\Team;

use App\Entity\AbstractEntity;
use App\Entity\User\User;
use App\Enum\SerializationGroup\Team\TeamMemberGroups;
use App\Enum\SerializationGroup\User\UserGroups;
use App\Enum\TeamMemberRole;
use App\Repository\TeamMemberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity(repositoryClass: TeamMemberRepository::class)]
#[ORM\Table]
#[ORM\UniqueConstraint(
    name: 'unique_team_invite',
    columns: ['team_id', 'email'],
)]
class TeamMember extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'teamMembers')]
    #[ORM\JoinColumn]
    #[Groups([
        TeamMemberGroups::SHOW,
        TeamMemberGroups::INDEX,
    ])]
    private Team $team;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn]
    #[Groups([
        TeamMemberGroups::SHOW,
        TeamMemberGroups::INDEX,
    ])]
    private User $user;

    #[ORM\Column(type: Types::STRING, enumType: TeamMemberRole::class)]
    #[Constraints\Choice(callback: [TeamMemberRole::class, 'cases'])]
    #[Groups([
        TeamMemberGroups::INVITE,
        TeamMemberGroups::SHOW,
        TeamMemberGroups::INDEX,
        TeamMemberGroups::UPDATE,
        TeamMemberGroups::ROLE,
    ])]
    #[Property(
        description: 'The role of a team member',
        enum: ['owner', 'manager', 'member'],
        example: 'manager',
    )]
    private TeamMemberRole $role;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [TeamMemberGroups::INVITE])]
    #[Constraints\Email]
    #[Groups([
        TeamMemberGroups::INVITE,
        TeamMemberGroups::SHOW,
        TeamMemberGroups::INDEX,
    ])]
    #[Property(
        description: 'Email of the invited team member',
    )]
    private string $email;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups([
        TeamMemberGroups::SHOW,
        TeamMemberGroups::INDEX,
    ])]
    #[Property(
        description: 'Whether or not the team member accepted the invite',
    )]
    private bool $accepted = false;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getRole(): TeamMemberRole
    {
        return $this->role;
    }

    public function setRole(TeamMemberRole $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isAccepted(): bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }
}