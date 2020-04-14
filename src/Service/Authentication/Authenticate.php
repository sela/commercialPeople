<?php

namespace App\Service\Authentication;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Util\JwtUtilInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class Authenticate implements AuthenticateInterface
{
    /** @var UserRepository  */
    private $userRepository;

    /** @var EntityManagerInterface  */
    private $entityManager;

    /** @var UserPasswordEncoderInterface  */
    private $userPasswordEncoder;

    /** @var JwtUtilInterface  */
    private $jwtUtil;

    /** @var string  */
    private $jwtTtl;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        JwtUtilInterface $jwtUtil,
        string $jwtTtl
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->jwtUtil = $jwtUtil;
        $this->jwtTtl = $jwtTtl;
    }

    /**
     * @inheritdoc
     */
    public function login(string $content)
    {
        $data = json_decode($content, true);

        $user = null;
        $user = $this->userRepository->findOneActiveByUsername($data['username']);
        if (
            !$user instanceof User ||
            !$this->userPasswordEncoder->isPasswordValid($user, $data['password'])
        ) {
            throw new UnauthorizedHttpException('Basic realm="API Login"', 'Invalid credentials.');
        }

        $id = uuid_create(UUID_TYPE_RANDOM);;
        $createdAt = (new DateTime())->format(DATE_ISO8601);
        $expiresAt = (new DateTime())->modify($this->jwtTtl)->format(DATE_ISO8601);

        $tokenData = [
            'id' => $id,
            'created_at' => $createdAt,
            'expires_at' => $expiresAt,
            'user' => [
                'id' => $user->getId(),
                'roles' => $user->getRoles(),
            ],
        ];

        $token = new Token();
        $token->setId($id);
        $token->setCreatedAt($createdAt);
        $token->setExpiresAt($expiresAt);
        $token->setUser($user);
        $token->setData($this->jwtUtil->encode($tokenData));

        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $token;
    }

}