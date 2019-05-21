<?php


namespace App\Tests\Validator;


use App\Validator\UniqueUsername;
use PHPUnit\Framework\TestCase;

class UniqueUsernameTest extends TestCase
{
    public function testMessage()
    {
        $constraint = new UniqueUsername(['message' => 'test']);
        $this->assertEquals('test', $constraint->message);
    }

}
