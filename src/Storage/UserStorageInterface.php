<?php

namespace App\Storage;

use App\Entity\User;

interface UserStorageInterface
{
    function save(User $user);

    function findOneByUsername(string $username): ?User;

    function findOneById(string $id): ?User;
}
