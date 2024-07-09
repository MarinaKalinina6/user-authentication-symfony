<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\GetUserService;
use App\Service\RemoveUserService;
use App\Service\UpdateUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserMethodsController extends AbstractController
{
    #[Route('/api/user/remove', methods: ['DELETE'])]
    public function remove(Request $request, RemoveUserService $removeUserService): Response
    {
        $response = $removeUserService->removeUser($request)->getContent();

        return $this->json(['massage' => $response]);
    }

    /**
     * @throws JsonException
     */
    #[Route('/api/user/update', methods: ['PUT'])]
    public function update(Request $request, UpdateUserService $updateUserService): Response
    {
        $response = $updateUserService->updateUser($request)->getContent();

        return $this->json(['massage' => $response]);
    }

    #[Route('/api/user', methods: ['GET'])]
    public function get(Request $request, GetUserService $getUser): Response
    {
        $response = $getUser->getData($request);

        return $this->json(['massage' => $response]);
    }

}
