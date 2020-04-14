<?php

namespace App\DataFixtures;

use App\Entity\League;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * AppFixtures constructor.
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $password = 'pass_1234';

        $user = new User();
        $user->setUsername('sela@sela.com');
        $user->setEmail('sela@sela.com');
        $user->setRoles(['ROLE_API']);
        $password = $this->userPasswordEncoder->encodePassword($user, $password);
        $user->setPassword($password);
        $manager->persist($user);

        // $product = new Product();
        // $manager->persist($product);
        $league = new League();
        $league->setName('UK Premier League');
        $manager->persist($league);

        $team = new Team();
        $team->setName('Chelsea FC');
        $team->setStrip('Adidas Blue');
        $team->setLeague($league);
        $manager->persist($team);

        $team = new Team();
        $team->setName('Arsenal FC');
        $team->setStrip('Adidas Red');
        $team->setLeague($league);
        $manager->persist($team);

        $manager->flush();
    }
}
