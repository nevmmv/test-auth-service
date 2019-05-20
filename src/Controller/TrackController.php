<?php

namespace App\Controller;

use App\Track\Command\TrackActionCommand;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @param MessageBusInterface $bus
     * @return Response
     * @throws \Exception
     */
    public function track(Request $request, MessageBusInterface $bus): Response
    {
        $name = $request->request->get('name');

        try {
            if (!$this->getUser() && !$request->getSession()->has('idUser')) {
                $request->getSession()->set('idUser', Uuid::uuid4());
            }
            if ($this->getUser()) {
                $idUser = $this->getUser()->getId();
            } else {
                $idUser = $request->getSession()->get('idUser');
            }

            $bus->dispatch(
                new Envelope(
                    new TrackActionCommand((string)Uuid::uuid4(), (string)$name, (string)$idUser, new \DateTimeImmutable()),
                    new SerializerStamp(['groups' => ['track']])
                )
            );
        }catch (\Throwable $exception){
            $this->json([
                'status' => 'Error',
                'data' => [
                    'name' => $name
                ],
                'code' => 500
            ]);
        }


        return $this->json([
            'status' => 'OK',
            'data' => [
                'name' => $name
            ],
            'code' => 200
        ]);
    }
}
