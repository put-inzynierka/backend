<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\User\ActivationToken;
use App\Enum\SerializationGroup\User\ActivationTokenGroups;
use App\Enum\SerializationGroup\User\UserGroups;
use App\Service\Instantiator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class ActivationTokenController extends AbstractController
{
    #[Rest\Patch(
        path: '/activation-token/{value}/activate',
        name: 'activate_activation_token',
        requirements: ['value' => '[0-9a-f]+']
    )]
    #[Tag('User')]
    #[Param\Path('value', description: 'The token to activate')]
    #[ParamConverter(data: ['name' => 'token'], class: ActivationToken::class)]
    #[Resp\EmptyResponse(
        description: 'Activates the user associated with the token and returns them',
    )]
    public function activate(
        Instantiator $instantiator,
        ActivationToken $token,
        EntityManagerInterface $entityManager
    ): Response {
        $instantiator->validate($token, ActivationTokenGroups::ACTIVATE);

        $user = $token->getUser();

        $token->setUsed(true);
        $user->setActive(true);

        $entityManager->persist($token);
        $entityManager->persist($user);

        $entityManager->flush();

        return $this->object(
            $user,
            200,
            UserGroups::SHOW
        );
    }
}
