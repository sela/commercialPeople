<?php

namespace App\Service\Authentication;

use App\Entity\Token;

interface AuthenticateInterface
{
    /**
     * @param string $content
     * @return Token
     */
    public function login(string $content);
}