<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Model\BodyRequest;
use App\Model\ErrorsRegisterUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterService extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ValidatorInterface $validator,
        private readonly BodyRequest $bodyRequest,
        private readonly ErrorsRegisterUser $errorsRegisterUser,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function addUser($request): Response
    {
        $body = $this->bodyRequest->decodeBody($request);
        $username = $body['username'] ?? null;
        $password = $body['password'] ?? null;
        if ($username === null || $password === null) {
            throw new BadCredentialsException('Need username and password!');
        }
        $user = new User($username);

        $user->setPassword($password);
        $errors = $this->validator->validate($user);
        $this->errorsRegisterUser->validateUserData($errors);

        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $user,
            $body['password'],
        );
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            if ($e->getCode() === 1062) {
                throw new BadCredentialsException('Username already exists');
            } else {
                throw new BadCredentialsException($e->getMessage());
            }
        }

        return new Response('Success! User register', Response::HTTP_OK);
    }

}