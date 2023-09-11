<?php declare(strict_types=1);

namespace App\Entity\Event;

use App\Entity\AbstractEntity;
use App\Enum\SerializationGroup\Event\AnnouncementGroups;
use App\Repository\AnnouncementRepository;
use Doctrine\DBAL\Types\Types;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
#[ORM\Table]
class Announcement extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Event $event;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [AnnouncementGroups::CREATE])]
    #[Groups([
        AnnouncementGroups::CREATE,
        AnnouncementGroups::SHOW,
        AnnouncementGroups::INDEX,
        AnnouncementGroups::UPDATE,
    ])]
    #[Property(
        description: 'The title of the announcement',
        example: 'Free hot dogs',
    )]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1)]
    #[Constraints\NotBlank(allowNull: false, groups: [AnnouncementGroups::CREATE])]
    #[Groups([
        AnnouncementGroups::CREATE,
        AnnouncementGroups::SHOW,
        AnnouncementGroups::INDEX,
        AnnouncementGroups::UPDATE,
    ])]
    #[Property(
        description: 'The description of the announcement',
        example: 'Come to pawilon A to get your free hot dog.',
    )]
    private string $description;

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
}