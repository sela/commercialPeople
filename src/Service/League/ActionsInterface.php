<?php

namespace App\Service\League;

use App\Entity\League;

interface ActionsInterface
{
    /**
     * @param League $league
     * @return bool
     */
    public function delete(League $league);

}