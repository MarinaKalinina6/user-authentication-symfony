<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LoginService;
use App\Service\RegisterService;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAuthenticationController extends AbstractController
{
    /**
     * @throws JsonException
     */
    #[Route('/api/register', methods: ['POST'])]
    public function register(Request $request, RegisterService $registerService): Response
    {
        $response = $registerService->addUser($request)->getContent();

        return $this->json(['massage' => $response], Response::HTTP_CREATED);
    }

    /**
     * @throws RandomException|JsonException
     */
    #[Route('/api/login', methods: ['POST'])]
    public function login(Request $request, LoginService $loginService): Response
    {
        $token = $loginService->check($request);

        return $this->json(['massage' => 'Success!', 'token' => $token], Response::HTTP_OK);
    }

}