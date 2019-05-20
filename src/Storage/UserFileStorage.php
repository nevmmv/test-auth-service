<?php

namespace App\Storage;

use App\Entity\User;
use App\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class UserFileStorage
 * @package App\Storage
 */
class UserFileStorage implements UserStorageInterface
{
    /**
     * @var string
     */
    private $dir = __DIR__ . '/../../storage/users';
    /**
     * @var string
     */
    private $ext = '.json';

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * UserFileStorage constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $usersStoragePath
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, string $usersStoragePath)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->dir = $usersStoragePath;
    }

    /**
     * @param User $user
     */
    public function save(User $user)
    {
        $event = new GenericEvent($user);
        $this->eventDispatcher->dispatch(Events::PRE_SAVE, $event);
        file_put_contents($this->dir . '/' . $user->getId() . $this->ext, $this->encode($user), LOCK_EX);
        $this->eventDispatcher->dispatch(Events::POST_SAVE, new GenericEvent($user));
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function findOneByUsername(string $username): ?User
    {
        /**
         * @var $file SplFileInfo
         */
        foreach ($this->getFinder() as $file) {
            $data = $this->decode($file->getContents());
            if ($data->username === $username) {
                return $this->mapToUser($data);
            }
        }
        return null;
    }

    /**
     * @param string $id
     * @return User|null
     */
    public function findOneById(string $id): ?User
    {
        $files = $this->getFinder()->name($id . $this->ext);
        /**
         * @var $file SplFileInfo
         */
        foreach ($files as $file) {
            $data = $this->decode($file->getContents());
            if ($data->id === $id) {
                return $this->mapToUser($data);
            }
        }
        return null;
    }

    /**
     * @param $content
     * @return mixed
     */
    private function decode($content)
    {
        return \json_decode($content);
    }

    /**
     * @param $content
     * @return false|string
     */
    private function encode($content)
    {
        return \json_encode($content);
    }

    /**
     * @param $data
     * @return User
     */
    private function mapToUser($data)
    {
        $user = new User($data->id, $data->firstname, $data->lastname, $data->username, $data->birthday);
        $user->setPassword($data->password);
        $user->setRoles($data->roles ?? []);
        return $user;
    }

    /**
     * @return Finder
     */
    protected function getFinder()
    {
        $finder = new Finder();
        return $finder->files()->in($this->dir);
    }
}
