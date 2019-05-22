<?php

namespace App\Controller;

use App\Track\Command\TrackActionCommand;
use App\Utils\GeneratorInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\SerializerStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TrackController
 * @package App\Controller
 */
class TrackController extends AbstractController
{
    /**
     * @Route("/track", name="app_track")
     * @param Request $request
     * @param GeneratorInterface $generator
     * @param MessageBusInterface $bus
     * @return Response
     */
    public function track(Request $request, GeneratorInterface $generator, MessageBusInterface $bus): Response
    {
        $name = $request->request->get('name');

        try {
            $idUser = $this->getUserId($request, $generator);

            $bus->dispatch(
                new Envelope(
                    new TrackActionCommand((string)Uuid::uuid4(), (string)$name, $idUser, new \DateTimeImmutable()),
                    new SerializerStamp(['groups' => ['track']])
                )
            );
            return new JsonResponse([
                'status' => 'OK',
                'data' => [
                    'name' => $name
                ],
                'code' => 200
            ]);
        } catch (\Throwable $exception) {
            return new JsonResponse([
                'status' => 'Error',
                'data' => [
                    'name' => $name
                ],
                'code' => 500
            ]);
        }
    }

    private function getUserId(Request $request, GeneratorInterface $generator)
    {
        if (!$this->getUser() && !$request->getSession()->has('idUser')) {

            $request->getSession()->set('idUser', $generator->generate());
        }
        if ($this->getUser()) {
            return $this->getUser()->getId();
        }

        return $request->getSession()->get('idUser');
    }
}
