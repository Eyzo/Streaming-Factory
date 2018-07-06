<?php
namespace Framework;

use Framework\Auth\UserInterface;

interface Auth
{

    /**
     * @return User|null
     */
    public function getUser(): ?UserInterface;
}