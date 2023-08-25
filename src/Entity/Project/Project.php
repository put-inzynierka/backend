<?php declare(strict_types=1);

namespace App\Entity\Project;

use App\Entity\AbstractEntity;
use App\Entity\File\File;
use App\Entity\Team\Team;
use App\Enum\ProjectType;
use App\Enum\SerializationGroup\Project\ProjectGroups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
class Project extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [ProjectGroups::CREATE])]
    #[Groups([
        ProjectGroups::CREATE,
        ProjectGroups::SHOW,
        ProjectGroups::INDEX,
        ProjectGroups::UPDATE,
    ])]
    #[Property(
        description: 'The name of the project',
        example: 'Line follower',
    )]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1)]
    #[Constraints\NotBlank(allowNull: false, groups: [ProjectGroups::CREATE])]
    #[Groups([
        ProjectGroups::CREATE,
        ProjectGroups::SHOW,
        ProjectGroups::UPDATE,
    ])]
    #[Property(
        description: 'The description of the project',
        example: 'Our line follower like to follow lines',
    )]
    private string $description;

    #[ORM\Column(type: Types::STRING, enumType: ProjectType::class)]
    #[Constraints\Choice(callback: [ProjectType::class, 'cases'])]
    #[Groups([
        ProjectGroups::SHOW,
        ProjectGroups::UPDATE,
        ProjectGroups::CREATE,
    ])]
    #[Property(
        description: 'Type of the project',
        enum: ['project_stand', 'lecture_panel'],
        example: 'project_stand',
    )]
    private ProjectType $projectType;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Constraints\NotBlank(allowNull: false, groups: [ProjectGroups::CREATE])]
    #[Groups([
        ProjectGroups::SHOW,
        ProjectGroups::CREATE,
        ProjectGroups::INDEX,
    ])]
    #[Property(
        description: 'Team responsible for the project',
    )]
    private Team $team;

    #[ORM\ManyToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(referencedColumnName: 'uuid', nullable: false)]
    #[Constraints\NotBlank(allowNull: false, groups: [ProjectGroups::CREATE])]
    #[Groups([
        ProjectGroups::CREATE,
        ProjectGroups::SHOW,
        ProjectGroups::INDEX,
        ProjectGroups::UPDATE,
    ])]
    #[Property(
        description: 'The image of the project',
    )]
    private File $image;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Reservation::class, cascade: ['remove'])]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProjectType(): ProjectType
    {
        return $this->projectType;
    }

    public function setProjectType(ProjectType $projectType): self
    {
        $this->projectType = $projectType;

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

    public function getImage(): File
    {
        return $this->image;
    }

    public function setImage(File $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setProject($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        $this->reservations->removeElement($reservation);

        return $this;
    }
}