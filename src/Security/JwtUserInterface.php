<?php
namespace App\Security;

use App\Entity\User;

interface JwtUserInterface
{
    public function get(): User;
}