<?php

namespace App\Tests\Service\Authentication;

use App\Entity\User;
use App\Entity\Token;
use App\Repository\UserRepository;
use App\Service\Authentication\Authenticate;
use App\Util\JwtUtilInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthenticateTest extends TestCase
{

    /**
     * @var MockObject|UserRepository
     */
    private $userRepository;

    /**
     * @var MockObject|EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MockObject|UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @var MockObject|JwtUtilInterface
     */
    private $jwtUtil;

    /**
     * @var string
     */
    private $ttl;

    public function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userPasswordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->jwtUtil = $this->createMock(JwtUtilInterface::class);
        $this->ttl = '+1 hour';
    }

    public function testNotAuthenticateLogin()
    {
        $authenticate = new Authenticate(
            $this->userRepository,
            $this->entityManager,
            $this->userPasswordEncoder,
            $this->jwtUtil,
            $this->ttl
        );
        $this->expectException(UnauthorizedHttpException::class);
        $authenticate->login('{"username": "sela@sela.com","password":"pass_1234"}');
    }

    public function testLogin()
    {
        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findOneActiveByUsername')
            ->willReturn($user);
        $this->userPasswordEncoder
            ->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(true);

        $authenticate = new Authenticate(
            $this->userRepository,
            $this->entityManager,
            $this->userPasswordEncoder,
            $this->jwtUtil,
            $this->ttl
        );
        $token = $authenticate->login('{"username": "sela@sela.com","password":"pass_1234"}');
        $this->assertInstanceOf(Token::class, $token);
    }

}
