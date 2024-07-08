<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class ErrorsRegisterUser extends AbstractController
{
    public function validateUserData($errors): Response
    {
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $value = $error->getPropertyPath();
                $message = $value.' - '.$error->getMessage();
                throw new BadCredentialsException($message);
            }
        }

        return new Response('ok');
    }
}