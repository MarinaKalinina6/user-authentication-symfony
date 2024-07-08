<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Security\AccessTokenHandler;
use App\Security\ApiKeyAuthenticator;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class GetUser extends ApiKeyAuthenticator
{
    public function __construct(
        private readonly AccessTokenHandler $tokenHandler,
        private readonly UserRepository $repository,
    ) {
    }

    public function getData($request): array
    {
        $this->authenticate($request);
        $headerAuthor = $request->headers->get('Authorization');

        $tokenFromHeader = $this->tokenHandler->getUserBadgeFrom($headerAuthor)->getUserIdentifier();

        $user = $this->repository->findOneBy(['token' => $tokenFromHeader]);
        if ($user === null) {
            throw new BadCredentialsException('User not found');
        }

        return [
            'username' => $user->getUsername(),
            'createdAt' => $user->getAddedAt()->format('Y-m-d H:i:s'),
        ];
    }

}