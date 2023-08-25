<?php

namespace App\Controller\Project;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Location\Location;
use App\Entity\Location\Stand;
use App\Enum\SerializationGroup\Location\StandGroups;
use App\Helper\Paginator;
use App\Repository\StandRepository;
use App\Service\Instantiator;
use App\Voter\Qualifier;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

class ReservationController extends AbstractController
{
    #[Rest\Post(
        path: '/projects/{id}/reservations',
        name: 'store_project_reservation',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Project')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the project',
    )]
    #[Param\Instance(Project::class, StandGroups::CREATE)]
    #[ParamConverter(data: ['name' => 'location'], class: Location::class)]
    #[Resp\ObjectResponse(
        description: 'Creates a new stand for the location',
        class: Stand::class,
        group: StandGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager,
        Location $location,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        /** @var Stand $stand */
        $stand = $instantiator->deserialize(
            $request->getContent(),
            Stand::class,
            StandGroups::CREATE
        );
        $stand->setLocation($location);

        $manager->persist($stand);
        $manager->flush();

        return $this->object(
            $stand,
            201,
            StandGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/locations/{location_id}/stands/{id}',
        name: 'update_location_stand',
        requirements: [
            'location_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Location')]
    #[Param\Path(
        name: 'location_id',
        description: 'The ID of the location',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the cast member',
    )]
    #[Param\Instance(Stand::class, StandGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'location'], class: Location::class, options: ['id' => 'location_id'])]
    #[ParamConverter(data: ['name' => 'stand'], class: Stand::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific cast member for the movie',
        class: Stand::class,
        group: StandGroups::SHOW,
    )]
    public function update(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $manager,
        Location $location,
        Stand $stand
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        $stand = $instantiator->deserialize(
            $request->getContent(),
            Stand::class,
             StandGroups::UPDATE,
            $stand
        );

        $manager->persist($stand);
        $manager->flush();

        return $this->object($stand, groups: StandGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/locations/{location_id}/stands/{id}',
        name: 'remove_location_stand',
        requirements: [
            'location_id' => '\d+',
            'id' => '\d+'
        ]
    )]
    #[Tag('Location')]
    #[Param\Path(
        name: 'location_id',
        description: 'The ID of the location',
    )]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the cast member',
    )]
    #[ParamConverter(data: ['name' => 'location'], class: Location::class, options: ['id' => 'location_id'])]
    #[ParamConverter(data: ['name' => 'stand'], class: Stand::class)]
    #[Resp\EmptyResponse(
        description: 'Removes the specific cast member from the movie',
    )]
    public function remove(
        EntityManagerInterface $manager,
        Location $location,
        Stand $stand
    ): Response {
        $this->denyAccessUnlessGranted(Qualifier::IS_ADMIN);

        $manager->remove($stand);
        $manager->flush();

        return $this->empty();
    }
}
