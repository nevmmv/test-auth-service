<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Tests\Constraints;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Storage\UserStorageInterface;
use App\Tests\UserDataTrait;
use App\Validator\UniqueUsername;
use App\Validator\UniqueUsernameValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class UniqueUsernameValidatorTest extends ConstraintValidatorTestCase
{
    use UserDataTrait;

    protected function createValidator()
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

        return new UniqueUsernameValidator(new UserRepository($userStorage));
    }

    /**
     * @dataProvider getValidValues
     * @param $value
     */
    public function testValidValues($value)
    {
        $this->validator->validate($value, new UniqueUsername());
        $this->assertNoViolation();
    }

    public function getValidValues()
    {
        yield ['test1'];
        yield ['test2'];
        yield ['test3'];
        yield ['test4'];;
    }

    public function testUsernameNotUnique()
    {
        $constraint = new UniqueUsername([
            'message' => 'myMessage',
        ]);
        $this->assertEquals('myMessage', $constraint->message);

        $this->validator->validate('username', $constraint);

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', 'username')
            ->addViolation();

    }
}
