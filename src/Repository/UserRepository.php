<?php


namespace App\Repository;


use App\Entity\User;
use App\Storage\UserStorageInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var UserStorageInterface
     */
    private $storage;

    public function __construct(UserStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function saveUser(User $user)
    {
        $this->storage->save($user);
    }

    public function findOneByUsername(string $username)
    {
        return $this->storage->findOneByUsername($username);
    }

    public function findOneById(string $id)
    {
        return $this->storage->findOneById($id);
    }

}
