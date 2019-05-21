<?php


namespace App\Tests\Utils;


use App\Utils\UuidGenerator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $generator = new UuidGenerator();
        $id = $generator->generate();
        $this->assertInstanceOf(UuidInterface::class, $id);
        $this->assertTrue(Uuid::isValid($id));
    }
}
