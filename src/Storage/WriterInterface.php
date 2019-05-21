<?php


namespace App\Storage;


interface WriterInterface
{
    public function write($path, $value);
}
