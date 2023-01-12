<?php

namespace App\Controller;

use App\Entity\CreateAccount;
use App\Entity\User;
use App\Form\CreateAccountType;
use App\Form\LoginType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/")
 */
class HomeController extends AbstractController
{

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("", name="home_index")
     */
    public function index(AnnonceRepository $annonceRepository, AuthenticationUtils $authenticationUtils, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $entityManager): Response
    {

        $error = null;
        $loginForm = null;
        $createForm = null;
        $success = null;

        if (!$this->getUser()) {
            $createForm = $this->createForm(CreateAccountType::class);
            $createForm->handleRequest($request);

            if ($createForm->isSubmitted() && $createForm->isValid()) {
                $data = $createForm->getData();
                $user = (new User())
                    ->setEmail($data['email'])
                    ->setRoles(['ROLE_GUEST']);
                $user->setPassword($hasher->hashPassword($user, $data['password']));
                $entityManager->persist($user);
                $createAccount = (new CreateAccount())
                    ->setUser($user)
                    ->setRole($data['role']);
                $entityManager->persist($createAccount);

                try {
                    $entityManager->flush();
                    $success = 'Votre compte a bien été crée, un consultant va examiner votre demande.';
                } catch (\Exception $exception) {
                    $createForm->addError(new FormError('Email déjà utilisé.'));
                }
            }

            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            $loginForm = $this->createForm(LoginType::class);
            $loginForm->get('email')->setData($lastUsername);

        } else {
            $role = $this->getUser()->getRoles()[0];
            if ($role !== "ROLE_GUEST") {
                return $this->redirectToRoute(strtolower(str_replace('ROLE_','', $role))."_index");
            }
        }

        $countAnnonce = $annonceRepository->count(['allowed' => true]);

        return $this->renderForm('home/index.html.twig', [
            'error' => $error,
            'loginForm' => $loginForm,
            'createForm' => $createForm,
            'success' => $success,
            'countAnnonce' => $countAnnonce,
        ]);
    }

    /**
     * @return void
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {

    }
}