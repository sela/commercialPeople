<?php

namespace App\Controller;

use App\Entity\League;
use App\Entity\Team;
use App\Service\League\ActionsInterface as LeagueActionsInterface;
use App\Service\Team\ActionsInterface as TeamActionsInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FootballController extends AbstractController
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
     * @var LeagueActionsInterface
     */
    private $leagueActions;
    /**
     * @var TeamActionsInterface
     */
    private $teamActions;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param LeagueActionsInterface $leagueActions
     * @param TeamActionsInterface $teamActions
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        LeagueActionsInterface $leagueActions,
        TeamActionsInterface $teamActions
    )
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->leagueActions = $leagueActions;
        $this->teamActions = $teamActions;
    }

    /**
     * @param League $league
     * @return JsonResponse
     * @Route("/league/{id}/team", methods={"GET"})
     */
    public function list(League $league): JsonResponse
    {
        return JsonResponse::fromJsonString($this->serializer->serialize($league->getTeams(),'json'));
    }


    /**
     * @param League $league
     * @param Request $request
     * @return JsonResponse
     * @Route("/league/{id}/team", methods={"POST"})
     */
    public function add(League $league, Request $request)
    {
        dd($league);
        $this->teamActions->add($league, $request->getContent());

        return new JsonResponse([], Response::HTTP_CREATED);
    }

    /**
     * @param Team $team
     * @param Request $request
     * @return JsonResponse
     * @Route("/team/{id}", methods={"PUT"})
     */
    public function replace(Team $team, Request $request): JsonResponse
    {
        $this->teamActions->update($team, $request->getContent());

        return new JsonResponse([], Response::HTTP_OK);
    }

    /**
     * @param League $league
     * @return JsonResponse
     * @Route("/league/{id}", methods={"DELETE"})
     */
    public function delete(League $league): JsonResponse
    {
        $this->leagueActions->delete($league);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

}
