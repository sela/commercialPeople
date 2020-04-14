<?php

namespace App\Repository;

use App\Entity\User;
use App\Util\JwtUtilInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use stdClass;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * @var JwtUtilInterface
     */
    private $jwtUtil;

    /**
     * @param ManagerRegistry $registry
     * @param JwtUtilInterface $jwtUtil
     */
    public function __construct(ManagerRegistry $registry, JwtUtilInterface $jwtUtil)
    {
        $this->jwtUtil = $jwtUtil;
        parent::__construct($registry, User::class);
    }

    /**
     * @param UserInterface $user
     * @param string $newEncodedPassword
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findOneActiveByUsername(string $username): ?User
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.email = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getOneOrNullResult(Query::HYDRATE_SIMPLEOBJECT);
    }

}
