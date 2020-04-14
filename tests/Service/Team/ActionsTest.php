<?php

namespace App\Tests\Service\Team;

use App\Entity\League;
use App\Entity\Team;
use App\Service\Team\Actions;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

class ActionsTest extends TestCase
{
    /**
     * @var MockObject|EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MockObject|League
     */
    private $league;

    /**
     * @var MockObject|Team
     */
    private $team;

    /**
     * @var MockObject|SerializerInterface
     */
    private $serializer;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->league = $this->createMock(League::class);
        $this->team = $this->createMock(Team::class);
    }

    public function testAdd()
    {
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($this->team);

        $actions = new Actions($this->entityManager, $this->serializer);
        $result = $actions->add($this->league, '');
        $this->assertTrue($result);
    }

    public function testFailedAdd()
    {
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($this->team);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->will($this->throwException(new ORMException()));

        $actions = new Actions($this->entityManager, $this->serializer);
        $result = $actions->add($this->league, '');
        $this->assertFalse($result);
    }

    public function testUpdate()
    {
        $this->team
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $this->team
            ->expects($this->once())
            ->method('getStrip')
            ->will($this->returnValue('foo'));
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($this->team);

        $actions = new Actions($this->entityManager, $this->serializer);
        $result = $actions->update($this->team, '');
        $this->assertTrue($result);
    }

    public function testFailedUpdate()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->will($this->throwException(new ORMException()));
        $this->team
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $this->team
            ->expects($this->once())
            ->method('getStrip')
            ->will($this->returnValue('foo'));
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($this->team);

        $actions = new Actions($this->entityManager, $this->serializer);
        $result = $actions->update($this->team, '');
        $this->assertFalse($result);
    }
}
