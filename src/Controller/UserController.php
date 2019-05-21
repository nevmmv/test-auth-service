<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    public function user(TokenStorageInterface $tokenStorage): Response
    {
        $user = $this->getUserFromToken($tokenStorage);


        return new JsonResponse([
            'user' => $user ? $user->getId() : $user
        ]);
    }

    /**
     * @Route("/register", name="app_register", methods={"POST"})
     * @param Request $request
     * @param UserRepositoryInterface $userRepository
     * @param FormFactoryInterface $formFactory
     * @return Response
     */
    public function register(Request $request, UserRepositoryInterface $userRepository, FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->create(RegistrationFormType::class, null, ['csrf_protection' => false]);

        $form->submit($request->request->all(), true);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = User::fromUserData($form->getData());
            $userRepository->saveUser($user);

            return new JsonResponse([
                'data' => $user->getId()
            ]);
        }

        return new JsonResponse([
            'errors' => $this->getErrorsFromForm($form)
        ]);

    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }

    private function getUserFromToken(TokenStorageInterface $tokenStorage)
    {
        if (null === $token = $tokenStorage->getToken()) {
            return null;
        }

        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }
}
