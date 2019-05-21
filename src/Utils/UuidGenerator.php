<?php


namespace App\Utils;


use Ramsey\Uuid\Uuid;

class UuidGenerator implements GeneratorInterface
{
    function generate()
    {
        return Uuid::uuid4();
    }
}
