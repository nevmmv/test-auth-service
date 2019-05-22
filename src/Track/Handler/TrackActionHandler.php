<?php


namespace App\Track\Handler;

use App\Track\Command\TrackActionCommand;
use SocialTech\StorageInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class TrackActionHandler
 * @package App\Track\Handler
 */
class TrackActionHandler implements MessageHandlerInterface
{
    /**
     * @var string
     */
    private $dir = __DIR__ . '/../../../storage/track';

    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * TrackActionHandler constructor.
     * @param StorageInterface $storage
     * @param SerializerInterface $serializer
     * @param string $tracksStoragePath
     */
    public function __construct(StorageInterface $storage, SerializerInterface $serializer, string $tracksStoragePath)
    {
        $this->storage = $storage;
        $this->serializer = $serializer;
        $this->dir = $tracksStoragePath;
    }

    /**
     * @param TrackActionCommand $command
     */
    public function __invoke(TrackActionCommand $command)
    {
        echo sprintf("Track#%s#%s" . PHP_EOL, $command->getId(), $command->getName());

        $serializedData = $this->serializer->serialize($command, 'json', ['groups' => ['track']]);

        $this->storage->store($this->dir . '/' . $command->getId() . '.json', $serializedData);
    }

}
