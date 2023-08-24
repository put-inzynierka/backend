<?php declare(strict_types=1);

namespace App\Entity\Team;

use App\Entity\AbstractEntity;
use App\Entity\Project\Project;
use App\Enum\SerializationGroup\Team\TeamGroups;
use App\Enum\TeamMemberRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
class Team extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [TeamGroups::CREATE])]
    #[Groups([
        TeamGroups::CREATE,
        TeamGroups::SHOW,
        TeamGroups::INDEX,
        TeamGroups::UPDATE,
    ])]
    #[Property(
        description: 'The name of the team',
        example: 'Drużyna A',
    )]
    private string $name;

    #[ORM\OneToMany(
        mappedBy: 'team',
        targetEntity: TeamMember::class,
        cascade: ['persist', 'remove']
    )]
    #[Groups([
        TeamGroups::SHOW,
    ])]
    private Collection $teamMembers;

    #[ORM\OneToMany(
        mappedBy: 'team',
        targetEntity: Project::class,
        cascade: ['persist', 'remove']
    )]
    #[Groups([
        TeamGroups::SHOW,
    ])]
    private Collection $projects;

    #[Groups([
        TeamGroups::INDEX,
    ])]
    private bool $editable = false;

    #[Groups([
        TeamGroups::SHOW,
    ])]
    private ?TeamMemberRole $myRole = null;

    public function __construct()
    {
        $this->teamMembers = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTeamMembers(): Collection
    {
        return $this->teamMembers;
    }

    public function setTeamMembers(Collection $teamMembers): self
    {
        $this->teamMembers = $teamMembers;

        return $this;
    }

    public function addTeamMember(TeamMember $teamMember): self
    {
        $this->teamMembers->add($teamMember);

        return $this;
    }

    public function removeTeamMember(TeamMember $teamMember): self
    {
        $this->teamMembers->removeElement($teamMember);

        return $this;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function setProjects(Collection $projects): self
    {
        $this->projects = $projects;

        return $this;
    }

    public function addProject(Project $project): self
    {
        $this->projects->add($project);

        return $this;
    }

    public function removeProject(Project $project): self
    {
        $this->projects->removeElement($project);

        return $this;
    }

    public function isEditable(): bool
    {
        return $this->editable;
    }

    public function setEditable(bool $editable): self
    {
        $this->editable = $editable;

        return $this;
    }

    public function getMyRole(): ?TeamMemberRole
    {
        return $this->myRole;
    }

    public function setMyRole(?TeamMemberRole $myRole): self
    {
        $this->myRole = $myRole;

        return $this;
    }
}