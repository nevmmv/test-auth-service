<?php


namespace App\Tests\Entity;


use App\Entity\User;
use App\Tests\UserDataTrait;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    use UserDataTrait;

    public function testGetSetUser()
    {
        $userData = $this->getUserData();
        $user = User::fromUserData($userData);
        $user->setPlainPassword($userData->getPassword());

        $this->assertNull($userData->getId(), $user->getId());
        $this->assertEquals($userData->getPassword(), $user->getPlainPassword());
        $this->assertEquals($userData->getUsername(), $user->getUsername());
        $this->assertEquals($userData->getLastname(), $user->getLastname());
        $this->assertEquals($userData->getFirstname(), $user->getFirstname());
    }
}

