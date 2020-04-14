<?php

namespace App\Security;

use App\Entity\User;
use App\Util\JwtUtilInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Serializer\SerializerInterface;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;


class JwtUserAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var JwtUtilInterface
     */
    private $jwtUtil;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * JwtUserAuthenticator constructor.
     */
    public function __construct(JwtUtilInterface $jwtUtil, SerializerInterface $serializer)
    {
        $this->jwtUtil = $jwtUtil;
        $this->serializer = $serializer;
    }

    public function supports(Request $request)
    {
        $token = $request->headers->has('Authorization');
        if (!$token) {
            throw new AccessDeniedHttpException();
        }

        return $token;
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        return $request->headers->get('Authorization');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        $tokenData = $this->validateToken($credentials);
        $user = $this->serializer->deserialize(json_encode($tokenData->user), User::class, 'json');

        // if a User is returned, checkCredentials() is called
        return $user;
    }

    private function validateToken(string $token): stdClass
    {
        preg_match('/^Bearer\s(\S+)/i', $token, $matches);
        if (!$matches) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        try {
            $tokenData = $this->jwtUtil->decode($matches[1]);
        } catch (Exception $e) {
            dd($e->getMessage());
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        $expiresAt = new DateTime($tokenData->expires_at);
        if ($expiresAt < new DateTime()) {
            throw new CustomUserMessageAuthenticationException('Token expired.');
        }

        return $tokenData;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // Check credentials - e.g. make sure the password is valid.
        // In case of an API token, no credential check is needed.

        // Return `true` to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }

}