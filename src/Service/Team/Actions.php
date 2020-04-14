<?php

namespace App\Service\Team;

use App\Entity\League;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;


class Actions implements ActionsInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function add(League $league, string $content)
    {
        try {
            $team = $this->serializer->deserialize($content, Team::class, 'json');
            $team->setLeague($league);
            $this->entityManager->persist($team);
            $this->entityManager->flush();
        }
        catch(\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function update(Team $team, string $content)
    {
        try {
            /** @var Team $teamData */
            $teamData = $this->serializer->deserialize($content, Team::class, 'json');
            $team->setName($teamData->getName());
            $team->setStrip($teamData->getStrip());
            $this->entityManager->persist($team);
            $this->entityManager->flush();
        }
        catch(\Exception $exception) {
            return false;
        }

        return true;
    }


}