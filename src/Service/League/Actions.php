<?php

namespace App\Service\League;

use App\Entity\League;
use Doctrine\ORM\EntityManagerInterface;


class Actions implements ActionsInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function delete(League $league)
    {
        try {
            $this->entityManager->remove($league);
            $this->entityManager->flush();
        }
        catch(\Exception $exception) {
            return false;
        }

        return true;
    }

}