<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\HttpClient\Exception\JsonException;

class BodyRequest
{
    /**
     * @throws JsonException
     */
    public function decodeBody($request)
    {
        try {
            $body = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new JsonException(sprintf('Filed to decode JSON: %s', $exception->getMessage()));
        }

        return $body;
    }


}