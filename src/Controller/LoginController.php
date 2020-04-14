<?php

namespace App\Controller;

use App\Service\Authentication\AuthenticateInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController
{

    /**
     * @var AuthenticateInterface
     */
    private $authenticate;

    public function __construct(AuthenticateInterface $authenticate)
    {
        $this->authenticate = $authenticate;
    }

    public function login(Request $request): Response
    {
        $token = $this->authenticate->login($request->getContent());

        return new Response($token->getData(), Response::HTTP_CREATED);
    }
}
