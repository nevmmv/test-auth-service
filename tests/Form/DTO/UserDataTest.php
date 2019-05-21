<?php


namespace App\Tests\Form\DTO;


use App\Tests\UserDataTrait;
use PHPUnit\Framework\TestCase;

class UserDataTest extends TestCase
{
    use UserDataTrait;

    public function testGetSetUserData()
    {
        $userData = $this->getUserData();
        $this->assertNull($userData->getId());
        $this->assertEquals($userData->getPassword(), 'password');
        $this->assertEquals($userData->getUsername(), 'username');
        $this->assertEquals($userData->getLastname(), 'lastname');
        $this->assertEquals($userData->getFirstname(), 'firstname');
    }
}

