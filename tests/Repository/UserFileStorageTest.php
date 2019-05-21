<?php


namespace App\Tests\Repository;


use App\Repository\UserRepository;
use App\Storage\UserFileStorage;
use App\Storage\WriterInterface;
use App\Tests\UserDataTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UserFileStorageTest extends TestCase
{
    use UserDataTrait;

    /**
     * @return \Generator
     */
    public function getCases()
    {
        yield [$this->getUserData()];
        yield [['test'=>$this->getUserData()]];
    }

    /**
     * @dataProvider getCases
     */
    public function testDecode($test)
    {
        $writer = $this->getMockBuilder(WriterInterface::class)->getMock();
        $eventDispatcher = new EventDispatcher();

        $userStorage = new UserFileStorage($eventDispatcher, $writer);


        $this->assertEquals(json_decode(json_encode($test)), $this->invokeMethod($userStorage, 'decode', [json_encode($test)]));

    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
