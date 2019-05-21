<?php


namespace App\Tests;


use App\Form\DTO\UserData;

trait UserDataTrait
{
    private function getUserData()
    {
        $userData = new UserData();
        $userData->setId(null)
            ->setPassword('password')
            ->setBirthday(date_create_immutable_from_format('Y/m/d H:i:s', '1995/12/13 00:00:00'))
            ->setFirstname('firstname')
            ->setLastname('lastname')
            ->setUsername('username');

        return $userData;
    }
}
