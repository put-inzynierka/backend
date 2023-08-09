<?php

namespace App\Controller;

use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Component\Model\PasswordResetRequest;
use App\Entity\User\PasswordResetToken;
use App\Entity\User\User;
use App\Enum\SerializationGroup\User\TokenGroups;
use App\Enum\SerializationGroup\User\UserGroups;
use App\Service\Instantiator;
use App\Service\User\PasswordHasher;
use App\Service\User\PasswordResetService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetTokenController extends AbstractController
{
    #[Rest\Post(path: '/password-reset-token', name: 'store_password_reset_token')]
    #[Tag('User')]
    #[Param\Instance(PasswordResetRequest::class, TokenGroups::CREATE)]
    #[Resp\EmptyResponse(
        description: 'Creates a new password reset token and sends it to the user',
    )]
    public function store(
        Instantiator $instantiator,
        Request $request,
        PasswordResetService $passwordResetService,
    ): Response {
        $passwordResetRequest = $instantiator->deserialize(
            $request->getContent(),
            PasswordResetRequest::class,
            TokenGroups::CREATE
        );

        $passwordResetService->sendPasswordResetLink($passwordResetRequest->getEmail());

        return $this->empty();
    }

    #[Rest\Patch(
        path: '/password-reset-token/{value}/invoke',
        name: 'invoke_activation_token',
        requirements: ['value' => '[0-9a-f]+']
    )]
    #[Tag('User')]
    #[Param\Path('value', description: 'The token to invoke')]
    #[ParamConverter(data: ['name' => 'token'], class: PasswordResetToken::class)]
    #[Resp\EmptyResponse(
        description: 'Changes password of the user associated with the token and returns them',
    )]
    public function invoke(
        Instantiator $instantiator,
        Request $request,
        EntityManagerInterface $entityManager,
        PasswordHasher $passwordHasher,
        PasswordResetToken $token
    ): Response {
        $instantiator->validate($token, TokenGroups::INVOKE);

        $user = $instantiator->deserialize(
            $request->getContent(),
            User::class,
            UserGroups::RESET_PASSWORD,
            $token->getUser()
        );
        $passwordHasher->hashPassword($user);

        $token->setUsed(true);

        $entityManager->persist($user);
        $entityManager->persist($token);

        $entityManager->flush();

        return $this->object(
            $user,
            200,
            UserGroups::SHOW
        );
    }
}
