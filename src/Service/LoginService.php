<?php

namespace App\Service;

use App\Entity\User;
use App\Model\BodyRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class LoginService extends AbstractController
{
    public function __construct(
        private readonly BodyRequest $bodyRequest,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $repository,
    ) {
    }

    /**
     * @throws RandomException|JsonException
     */
    public function check($request): string
    {
        $body = $this->bodyRequest->decodeBody($request);
        $username = $body['username'] ?? null;
        $password = $body['password'] ?? null;
        if ($username === null || $password === null) {
            throw new BadCredentialsException('Need username and password!');
        }

        $currentUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if (empty($currentUser)) {
            throw new BadCredentialsException('User not found');
        }
        if (!$this->passwordHasher->isPasswordValid($currentUser, $password)) {
            throw new BadCredentialsException('Wrong password');
        }

        $currentToken = $currentUser->getToken();
        if ($currentToken == null) {
            return $this->createToken($currentUser);
        }
        $validToken = $this->repository->isValidToken($currentToken);

        if ($validToken) {
            return $currentToken;
        } else {
            return $this->createToken($currentUser);
        }
    }

    /**
     * @throws RandomException
     */
    public
    function createToken(
        $currentUser,
    ): string {
        try {
            $bytes = random_bytes(14);
            $token = bin2hex($bytes);
            $currentUser->setToken($token)
                ->setCreatedToken();
            $this->entityManager->persist($currentUser);
            $this->entityManager->flush();

            return $token;
        } catch (RandomException $e) {
            throw new RandomException('Error generating the token: '.$e->getMessage());
        }
    }
}