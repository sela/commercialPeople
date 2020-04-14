<?php
/**
 * Created by PhpStorm.
 * User: selayair
 * Date: 13/04/2020
 * Time: 18:40
 */
namespace App\Service\Team;

use App\Entity\League;
use App\Entity\Team;

interface ActionsInterface
{
    /**
     * @param League $league
     * @param string $content
     * @return bool
     */
    public function add(League $league, string $content);

    /**
     * @param Team $team
     * @param string $content
     * @return bool
     */
    public function update(Team $team, string $content);

}