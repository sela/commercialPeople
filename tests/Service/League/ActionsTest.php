<?php

namespace App\Tests\Service\League;

use App\Entity\League;
use App\Service\League\Actions;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;


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

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->league = $this->createMock(League::class);
    }


    public function testDelete()
    {
        $actions = new Actions($this->entityManager);
        $result = $actions->delete($this->league);
        $this->assertTrue($result);
    }

    public function testFailedDelete()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->will($this->throwException(new ORMException()));
        $actions = new Actions($this->entityManager);
        $result = $actions->delete($this->league);
        $this->assertFalse($result);
    }

}
