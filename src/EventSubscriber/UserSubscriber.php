<?php


namespace App\EventSubscriber;


use App\Entity\User;
use App\Events;
use App\Utils\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserSubscriber
 * @package App\EventSubscriber
 */
class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * UserSubscriber constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GeneratorInterface $generator
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, GeneratorInterface $generator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->generator = $generator;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::PRE_SAVE => [['generateId', 1], ['encodePassword', 2]]
        ];
    }

    /**
     * @param GenericEvent $event
     */
    public function encodePassword(GenericEvent $event)
    {
        $user = $event->getSubject();
        if (!$user instanceof User) {
            return;
        }

        if ($user->getPlainPassword()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
        }
    }

    /**
     * @param GenericEvent $event
     * @throws \Exception
     */
    public function generateId(GenericEvent $event)
    {
        $user = $event->getSubject();
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getId()) {
            $user->setId($this->generator->generate());
        }
    }
}
