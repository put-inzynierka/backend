<?php

namespace App\Controller\Location;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Controller\AbstractController;
use App\Entity\Location\Location;
use App\Entity\Location\Stand;
use App\Enum\SerializationGroup\Location\StandGroups;
use App\Helper\Paginator;
use App\Repository\StandRepository;
use App\Service\Instantiator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

class StandController extends AbstractController
{
    #[Rest\Get(
        path: '/locations/{id}/stands',
        name: 'index_location_stands',
        requirements: ['id' => '\d+'],
    )]
    #[Tag('Location')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the location',
    )]
    #[Param\Limit]
    #[Param\Page]
    #[ParamConverter(data: ['name' => 'location'], class: Location::class)]
    #[Resp\PageResponse(
        description: 'Returns the list stands for the location',
        class: Stand::class,
        group: StandGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        StandRepository       $repository,
        Location              $location
    ): Response {
        $list = $repository->indexByLocation($location);

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: StandGroups::INDEX);
    }

    #[Rest\Get(
        path: '/locations/{location_id}/stands/{id}',
        name: 'show_location_stand',
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
        description: 'The ID of the role',
    )]
    #[ParamConverter(data: ['name' => 'stand'], class: Stand::class)]
    #[Resp\ObjectResponse(
        description: 'Shows the specific stand',
        class: Stand::class,
        group: StandGroups::SHOW,
    )]
    public function show(
        Stand $stand
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::HAS_ACCESS, $stand);

        return $this->object($stand, groups: StandGroups::SHOW);
    }

    #[Rest\Post(
        path: '/locations/{id}/stands',
        name: 'store_location_stand',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Location')]
    #[Param\Path(
        name: 'id',
        description: 'The ID of the location',
    )]
    #[Param\Instance(Stand::class, StandGroups::CREATE)]
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
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $location);

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
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $location);
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
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $location);
        $manager->remove($stand);
        $manager->flush();

        return $this->empty();
    }
}
