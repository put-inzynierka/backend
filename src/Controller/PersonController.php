<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\Person\Person;
use App\Enum\SerializationGroup\Person\PersonGroups;
use App\Helper\Paginator;
use App\Repository\RepositoryFactory;
use App\Service\Instantiator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class PersonController extends AbstractController
{
    #[Rest\Get(path: '/people', name: 'index_people')]
    #[Param\Limit]
    #[Param\Page]
    #[Resp\PageResponse(
        description: 'Returns the list of people',
        class: Person::class,
        group: PersonGroups::INDEX,
    )]
    public function index(
        ParamFetcherInterface $paramFetcher,
        RepositoryFactory $repositoryFactory
    ): Response {
        $repository = $repositoryFactory->create(Person::class);
        $list = $repository->index();

        $page = Paginator::paginate(
            $list,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->object($page, groups: PersonGroups::INDEX);
    }

    #[Rest\Get(
        path: '/people/{id}',
        name: 'show_person',
        requirements: ['id' => '\d+']
    )]
    #[Param\Path('id', description: 'The ID of the person')]
    #[ParamConverter(data: ['name' => 'person'], class: Person::class)]
    #[Resp\ObjectResponse(
        description: 'Returns details about the specific person',
        class: Person::class,
        group: PersonGroups::SHOW,
    )]
    public function show(
        Person $person
    ): Response {
        return $this->object($person, groups: PersonGroups::SHOW);
    }

    #[Rest\Post(path: '/people', name: 'store_person')]
    #[Param\Instance(Person::class, PersonGroups::CREATE)]
    #[Resp\ObjectResponse(
        description: 'Creates a new person',
        class: Person::class,
        group: PersonGroups::SHOW,
        status: 201,
    )]
    public function store(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_AUTHENTICATED);

        /** @var Person $person */
        $person = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Person::class,
            PersonGroups::CREATE
        );

        $manager->persist($person);
        $manager->flush();

        return $this->object(
            $person,
            201,
            PersonGroups::SHOW
        );
    }

    #[Rest\Patch(
        path: '/people/{id}',
        name: 'update_person',
        requirements: ['id' => '\d+']
    )]
    #[Param\Path('id', description: 'The ID of the person')]
    #[Param\Instance(Person::class, PersonGroups::UPDATE)]
    #[ParamConverter(data: ['name' => 'person'], class: Person::class)]
    #[Resp\ObjectResponse(
        description: 'Updates the specific person',
        class: Person::class,
        group: PersonGroups::SHOW,
    )]
    public function update(
        Instantiator $instantiator,
        ParamFetcherInterface $paramFetcher,
        EntityManagerInterface $manager,
        Person $person
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $person);

        $person = $instantiator->deserialize(
            $paramFetcher->get('instance'),
            Person::class,
            PersonGroups::UPDATE,
            $person
        );

        $manager->persist($person);
        $manager->flush();

        return $this->object($person, groups: PersonGroups::SHOW);
    }

    #[Rest\Delete(
        path: '/people/{id}',
        name: 'remove_person',
        requirements: ['id' => '\d+']
    )]
    #[Param\Path('id', description: 'The ID of the person')]
    #[ParamConverter(data: ['name' => 'person'], class: Person::class)]
    #[Resp\EmptyResponse(
        description: 'Removes the specific person',
        status: 204,
    )]
    public function remove(
        EntityManagerInterface $manager,
        Person $person
    ): Response {
//        $this->denyAccessUnlessGranted(Qualifier::IS_OWNER, $person);

        $manager->remove($person);
        $manager->flush();

        return $this->empty();
    }
}
