<?php


namespace App\Repository;


use App\Entity\User;

interface UserRepositoryInterface
{
    function saveUser(User $user);

    function findOneByUsername(string $username);

    function findOneById(string $id);
}
