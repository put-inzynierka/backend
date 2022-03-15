<?php

namespace App\Controller;

use App\Entity\User\User;
use App\Enum\SerializationGroup\BaseGroups;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractController extends AbstractFOSRestController
{
    protected function object(
        $object,
        int $statusCode = Response::HTTP_OK,
        $groups = []
    ): Response {
        $view = $this->view($object, $statusCode);

        if (gettype($groups) === 'string') {
            $groups = [$groups];
        }

        $this->buildContext($view, $groups);

        return $this->handleView($view);
    }

    protected function empty(int $statusCode = Response::HTTP_NO_CONTENT): Response
    {
        $view = $this->view(null, $statusCode);

        return $this->handleView($view);
    }

    protected function raw(
        array $data,
        int $statusCode = Response::HTTP_OK
    ): Response {
        $view = $this->view($data, $statusCode);

        return $this->handleView($view);
    }

    protected function buildContext(View $view, array $groups): void
    {
        $view->getContext()
            ->setGroups([BaseGroups::DEFAULT, ...$groups])
            ->setSerializeNull(true)
        ;
    }

    protected function getUser(): ?User
    {
        /** @var ?User $user */
        $user = parent::getUser();

        return $user;
    }
}
