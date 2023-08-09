<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\Location\Location;
use App\Entity\Movie\Genre;
use App\Enum\SerializationGroup\Location\LocationGroups;
use App\Helper\Paginator;
use App\Repository\RepositoryFactory;
use App\Service\Instantiator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes\Tag;

class LocationController extends AbstractController
{
    #[Rest\Get(path: '/locations', name: 'index_locations')]
    #[Tag('Location')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of locations',
        class: Location::class,
        group: LocationGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        RepositoryFactory $repositoryFactory
    ): Response {
        $repository = $repositoryFactory->create(Location::class);
        $list = $repository->index();

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: LocationGroups::INDEX);
    }

    #[Rest\Get(
        path: '/locations/{id}',
        name: 'show_location',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Location')]
    #[Param\Path('id', description: 'The ID of the location')]
    #[ParamConverter(data: ['name' => 'location'], class: Location::class)]
    #[Resp\ObjectResponse(
        description: 'Returns details about the specific location',
        class: Location::class,
        group: LocationGroups::SHOW,
    )]
    public function show(
        Location $location
    ): Response {
        return $this->object($location, groups: LocationGroups::SHOW);
    }

    #[Rest\Post(path: '/locations', name: 'store_location')]
    #[Tag('Location')]
    #[Param\Instance(Location::class, LocationGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new location',
        class: Location::class,
        group: LocationGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager,
        Request $request
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_AUTHENTICATED);

        /** @var Location $location */
        $location = $instantiator->deserialize(
            $request->getContent(),
            Location::class,
            LocationGroups::CREATE
        );

        $manager->persist($location);
        $manager->flush();

        return $this->object(
            $location,
            201,
            LocationGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/locations/{id}',
        name: 'update_location',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Location')]
    #[Param\Path('id', description: 'The ID of the location')]
    #[Param\Instance(Location::class, LocationGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'location'], class: Location::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific location',
        class: Location::class,
        group: LocationGroups::SHOW,
    )]
    public function update(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $manager,
        Location $location
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $location);

        $location = $instantiator->deserialize(
            $request->getContent(),
            Location::class,
            LocationGroups::UPDATE,
            $location
        );

        $manager->persist($location);
        $manager->flush();

        return $this->object($location, groups: LocationGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/locations/{id}',
        name: 'remove_location',
        requirements: ['id' => '\d+']
    )]
    #[Tag('Location')]
    #[Param\Path('id', description: 'The ID of the location')]
    #[ParamConverter(data: ['name' => 'location'], class: Location::class)]
    #[Resp\EmptyResponse(
        description: 'Removes the specific location',
        status: 204,
    )]
    public function remove(
        EntityManagerInterface $manager,
        Location $location
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $location);

        $manager->remove($location);
        $manager->flush();

        return $this->empty();
    }
}
