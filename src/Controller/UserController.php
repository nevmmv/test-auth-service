<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     * @return Response
     */
    public function user(): Response
    {

        return $this->json([
            'user' => $this->getUser()->getId()
        ]);
    }

    /**
     * @Route("/register", name="app_register", methods={"POST"})
     * @param Request $request
     * @param UserRepositoryInterface $userRepository
     * @return Response
     */
    public function register(Request $request, UserRepositoryInterface $userRepository): Response
    {
        $form = $this->createForm(RegistrationFormType::class, null, ['csrf_protection' => false]);

        $form->submit($request->request->all(), true);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = User::fromUserData($form->getData());
            $userRepository->saveUser($user);

            return $this->json([
                'data' => $user->getId()
            ]);
        }

        return $this->json([
            'errors' => $this->getErrorsFromForm($form)
        ]);

    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
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
}
