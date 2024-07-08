<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Security\AccessTokenHandler;
use App\Security\ApiKeyAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class RemoveUserService extends ApiKeyAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManage,
        private readonly UserRepository $repository,
        private readonly AccessTokenHandler $tokenHandler,
    ) {
    }

    public function removeUser($request): Response
    {
        $this->authenticate($request);
        $headerAuthor = $request->headers->get('Authorization');

        $tokenFromHeader = $this->tokenHandler->getUserBadgeFrom($headerAuthor)->getUserIdentifier();
        $user = $this->repository->findOneBy(['token' => $tokenFromHeader]);
        if ($user === null) {
            throw new BadCredentialsException('User not found.');
        }

        $this->entityManage->remove($user);
        $this->entityManage->flush();

        return new Response('Success! User remove', Response::HTTP_OK);
    }


}