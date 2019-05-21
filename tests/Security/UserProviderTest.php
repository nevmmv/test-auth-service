<?php


namespace App\Tests\Security;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserProvider;
use App\Storage\UserStorageInterface;
use App\Tests\UserDataTrait;
use PHPUnit\Framework\TestCase;

class UserProviderTest extends TestCase
{
    use UserDataTrait;

    public function getTestCasesFindByUsername()
    {
        yield [$this->getUserData()->getUsername(), User::fromUserData($this->getUserData())];
        yield ['test2', null];
        yield ['test3', null];
        yield ['test4', null];;
    }

    public function getTestCasesFindById()
    {
        yield [1, User::fromUserData($this->getUserData())->setId(1)];
        yield [2, User::fromUserData($this->getUserData())->setId(2)];
        yield [3, null];
        yield [4, null];;
    }

    private function mockUserStorage()
    {
        $userStorage = $this->getMockBuilder(UserStorageInterface::class)->getMock();
        $userStorage->expects($this->any())
            ->method('findOneByUsername')
            ->willReturnCallback(function ($username) {
                if ($this->getUserData()->getUsername() === $username) {
                    return User::fromUserData($this->getUserData());
                }
                return null;
            });
        $userStorage->expects($this->any())
            ->method('findOneById')
            ->willReturnCallback(function ($id) {
                if (in_array($id, [1, 2])) {
                    return User::fromUserData($this->getUserData())->setId($id);
                }
                return null;
            });
        return $userStorage;

    }

    /**
     * @dataProvider getTestCasesFindByUsername
     */
    public function testFindByUsername($username, $res)
    {

        $repository = new UserRepository($this->mockUserStorage());
        $provider = new UserProvider($repository);

        $user = $provider->loadUserByUsername($username);

        $this->assertEquals($res, $user);
    }

    /**
     * @dataProvider getTestCasesFindById
     */
    public function testFindById($id, $res)
    {

        $repository = new UserRepository($this->mockUserStorage());
        $provider = new UserProvider($repository);

        $userOld = User::fromUserData($this->getUserData())->setId($id);
        $user = $provider->refreshUser($userOld);

        $this->assertEquals($res, $user);
    }

}
