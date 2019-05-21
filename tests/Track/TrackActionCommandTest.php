<?php


namespace App\Tests\Track;


use App\Track\Command\TrackActionCommand;
use PHPUnit\Framework\TestCase;

class TrackActionCommandTest extends TestCase
{
    public function testGetSet()
    {
        $date = new \DateTimeImmutable();
        $trackId = 'trackID';
        $name = 'trackName';
        $idUser = 'userID';

        $command = new TrackActionCommand($trackId, $name, $idUser, $date);

        $this->assertEquals($trackId, $command->getId());
        $this->assertEquals($name, $command->getName());
        $this->assertEquals($date, $command->getDateCreated());
        $this->assertEquals($idUser, $command->getIdUser());
    }
}
