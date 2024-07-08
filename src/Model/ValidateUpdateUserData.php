<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ValidateUpdateUserData
{
    #[Assert\Length(['min' => 5, 'max' => 25])]
    private ?string $username;
    
    #[Assert\Length(['min' => 5, 'max' => 25])]
    private ?string $password;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

}