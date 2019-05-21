<?php


namespace App\Storage;


class JsonFileWriter implements WriterInterface
{

    public function write($path, $value)
    {
        file_put_contents($path, \json_encode($value), LOCK_EX);
    }
}
