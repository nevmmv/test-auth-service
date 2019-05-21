<?php

namespace App\Tests\Controller;

use App\Controller\UserController;
use App\Entity\User;
use App\Events;
use App\EventSubscriber\UserSubscriber;
use App\Form\DTO\UserData;
use App\Repository\UserRepository;
use App\Storage\JsonFileWriter;
use App\Storage\UserFileStorage;
use App\Storage\WriterInterface;
use App\Tests\UserDataTrait;
use App\Utils\GeneratorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserControllerTest extends TestCase
{
    use UserDataTrait;

    /**
     * @var User
     */
    private static $user;

    protected function setUp()
    {
        parent::setUp();
    }

    private function createController()
    {
        return new UserController();
    }


    private function mockIdGenerator($value)
    {
        $idGenerator = $this->getMockBuilder(GeneratorInterface::class)->getMock();

        $idGenerator
            ->expects($this->any())
            ->method('generate')
            ->willReturn($value);

        return $idGenerator;
    }

    private function mockTokenStorageWithUser($user)
    {
        $token = $this->getMockBuilder(TokenInterface::class)
            ->getMock();
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)
            ->getMock();
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        return $tokenStorage;
    }

    private function mockPasswordEncoder($encoded, $valid = true)
    {
        $passwordEncoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();

        $passwordEncoder
            ->expects($this->any())
            ->method('isPasswordValid')
            ->willReturn($valid);

        $passwordEncoder
            ->expects($this->any())
            ->method('encodePassword')
            ->willReturn($encoded);

        return $passwordEncoder;
    }

    public function testRegistrationWithPostMethodValidatesFormAndSaveUserWhenValid()
    {
        $eventDispatcher = new EventDispatcher();
        $passwordEncoder = $this->mockPasswordEncoder('encodedPassword');
        $idGenerator = $this->mockIdGenerator(120);

        $eventSubscriber = new UserSubscriber($passwordEncoder, $idGenerator);

        $this->assertFalse($eventDispatcher->hasListeners(Events::PRE_SAVE));
        $eventDispatcher->addSubscriber($eventSubscriber);
        $this->assertTrue($eventDispatcher->hasListeners(Events::PRE_SAVE));


        $userData = $this->getUserData();

        $request = new Request(
            [],
            [
                'username' => $userData->getUsername(),
                'password' => $userData->getPassword(),
                'firstname' => $userData->getFirstname(),
                'lastname' => $userData->getLastname(),
                'birthday' => $userData->getBirthday()->format('Y/m/d'),
            ]
        );
        $request->setMethod(Request::METHOD_POST);

        $writer = $this->getMockBuilder(WriterInterface::class)->getMock();

        $writer->expects($this->any())
            ->method('write')
            ->willReturnCallback(function ($path, $data) {
                self::$user = $data;
//                $writer = new JsonFileWriter();
//                $writer->write($path, $data);
                return $data;
            });


        $userRepository = new UserRepository(new UserFileStorage($eventDispatcher, $writer));

        $controller = $this->createController();

        // success
        $formFactory = $this->mockFormFactory($request->request->all(), $userData);
        $response = $controller->register($request, $userRepository, $formFactory);
        $this->assertInstanceOf(Response::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertNotNull($responseData);
        $this->assertInternalType('array', $responseData);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertNotNull($responseData['data']);
        $this->assertEquals($responseData['data'], self::$user->getId());

        //fail
        $formFactory = $this->mockFormFactory($request->request->all(), $userData, true, false);
        $responseFail = $controller->register($request, $userRepository, $formFactory);
        $responseData = json_decode($responseFail->getContent(), true);
        $this->assertNotNull($responseData);
        $this->assertInternalType('array', $responseData);
        $this->assertArrayHasKey('errors', $responseData);


        $this->assertTrue(true);
    }

    public function usersGenerator()
    {
        yield [self::$user];
        yield [null];
    }

    /**
     * @depends      testRegistrationWithPostMethodValidatesFormAndSaveUserWhenValid
     * @dataProvider usersGenerator
     */
    public function testAuthenticatedUserUser($user)
    {
        $controller = $this->createController();

        $tokenStorage = $this->mockTokenStorageWithUser($user);

        $response = $controller->user($tokenStorage);
        $this->assertInstanceOf(Response::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertNotNull($responseData);
        $this->assertInternalType('array', $responseData);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertEquals($responseData['user'], $user ? $user->getId() : $user);


    }

    private function mockForm($allParams, $data, $isSubmitted = true, $isValid = true)
    {
        $form = $this
            ->getMockBuilder(FormInterface::class)
            ->getMock();
        $form
            ->expects($this->any())
            ->method('submit')
            ->with($allParams, true);
        $form
            ->expects($this->any())
            ->method('isSubmitted')
            ->will($this->returnValue($isSubmitted));
        $form
            ->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue($isValid));
        $form
            ->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($data));

        return $form;
    }

    private function mockFormFactory(array $allParams, UserData $data, $isSubmitted = true, $isValid = true)
    {

        $form = $this->mockForm($allParams, $data, $isSubmitted, $isValid);
        if (!$isValid) {
            $form
                ->expects($this->any())
                ->method('getErrors')
                ->will($this->returnValue([]));

            $childForm = $this->mockForm($allParams, $data, $isSubmitted, $isValid);
            $childForm->expects($this->any())
                ->method('getErrors')
                ->will($this->returnValue([new FormError('Error!')]));
            $childForm
                ->expects($this->any())
                ->method('all')
                ->will($this->returnValue([]));
            $childForm
                ->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('username'));

            $form
                ->expects($this->any())
                ->method('all')
                ->will($this->returnValue(['username' => $childForm]));

        }
        $formFactory = $this->getMockBuilder(FormFactoryInterface::class)->getMock();
        $formFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        return $formFactory;
    }
}
