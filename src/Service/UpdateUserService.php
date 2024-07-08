<?php

namespace App\Service;

use App\Model\BodyRequest;
use App\Model\ErrorsRegisterUser;
use App\Model\ValidateUpdateUserData;
use App\Repository\UserRepository;
use App\Security\AccessTokenHandler;
use App\Security\ApiKeyAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateUserService extends ApiKeyAuthenticator
{
    public function __construct(
        private readonly BodyRequest $bodyRequest,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManage,
        private readonly AccessTokenHandler $tokenHandler,
        private readonly UserRepository $repository,
        private readonly ValidatorInterface $validator,
        private readonly ErrorsRegisterUser $errorsRegisterUser,
        private readonly ValidateUpdateUserData $validateData,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function updateUser($request): Response
    {
        $this->authenticate($request);
        $headerAuthor = $request->headers->get('Authorization');

        $tokenFromHeader = $this->tokenHandler->getUserBadgeFrom($headerAuthor)->getUserIdentifier();

        $user = $this->repository->findOneBy(['token' => $tokenFromHeader]);
        if ($user === null) {
            throw new BadCredentialsException('User not found');
        }

        $body = $this->bodyRequest->decodeBody($request);
        $username = $body['username'] ?? null;
        $password = $body['password'] ?? null;

        if ($username !== null) {
            $errors = $this->validator->validate($this->validateData->setUsername($username));
            $this->errorsRegisterUser->validateUserData($errors);

            $user->setUsername($this->validateData->getUsername());
        }

        if ($password !== null) {
            $errors = $this->validator->validate($this->validateData->setPassword($password));
            $this->errorsRegisterUser->validateUserData($errors);

            $user->setPassword($this->userPasswordHasher->hashPassword($user, $this->validateData->getPassword()));
        }
        $this->entityManage->persist($user);

        try {
            $this->entityManage->flush();
        } catch (\Exception $e) {
            if ($e->getCode() === 1062) {
                throw new BadCredentialsException('Username already exists');
            } else {
                throw new BadCredentialsException($e->getMessage());
            }
        }

        return new Response('Success! User update', Response::HTTP_OK);
    }

}