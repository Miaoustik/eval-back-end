<?php

namespace App\Controller;

use App\Entity\ProfilCandidat;
use App\Entity\ProfilRecruteur;
use App\Form\EmailModifyType;
use App\Form\PasswordModifyType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/profil")
 */
class ProfilController extends AbstractController
{

    private EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    /**
     * @return Response
     * @Route("", name="profil_index")
     */
    public function index(): Response
    {
        $this->setRepository();
        $profil = $this->repository->findOneByUser($this->getUser());
        if (!$profil) {
            return $this->redirectToRoute('profil_modify');
        }

        return $this->render('profil/index.html.twig', [
            'profil' => $profil,
        ]);
    }

    /**
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     * @Route("/modify", name="profil_modify")
     */
    public function modifyProfil(Request $request, SluggerInterface $slugger, UserPasswordHasherInterface $hasher): Response
    {
        $this->setRepository();
        $title = 'Modifier';
        $user = $this->getUser();
        $profil = $this->repository->findOneByUser($user);
        $profilName = $this->repository->getClassName();
        $formName = str_replace('Entity', 'Form', $profilName) . 'Type';

        if (!$profil) {
            $profil = new $profilName();
            $profil->setUser($user);
            $this->entityManager->persist($profil);
            $title = 'Editer';
        }

        $form = $this->createForm($formName, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $path = $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cv';

            /** @var ProfilCandidat|ProfilRecruteur $profil */
            $profil = $form->getData();
            if ($profil instanceof ProfilRecruteur) {
                $profil->setPostalCode(str_replace(' ', '', $profil->getPostalCode()));
            }

            if ($profil instanceof ProfilCandidat) {
                /** @var UploadedFile $cv */
                $cv = $form->get('cvFile')->getData();

                if ($cv) {
                    $cvOriginal = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
                    $cvSafe = $slugger->slug($cvOriginal);
                    $cvNewName = $cvSafe . '-' . uniqid() . '.' . $cv->guessExtension();
                    $cvPath = $path . DIRECTORY_SEPARATOR . $profil->getCvName();
                    try {
                        $cv->move($path, $cvNewName);
                        if ($profil->getCvName() !== null && file_exists($cvPath)) {
                            unlink($cvPath);
                        }
                        $profil->setCvName($cvNewName);
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur avec l\'envoi du cv.');
                    }
                }
            }

            try {
                $this->entityManager->flush();
                $this->addFlash('success', 'Votre profil a bien été enregistré.');
            } catch (\Exception $e) {
                $this->addFlash('error', "Problème avec la modification de profil.");
            }
        }
        $connectionForms = $this->connectionForms($request, $hasher);
        return $this->render('profil/modify.html.twig', array_merge([
            'profilForm' => $form->createView(),
            'profil' => $profil,
            'title' => $title,
        ], $connectionForms));
    }

    private function connectionForms(Request $request, UserPasswordHasherInterface $hasher)
    {
        $user = $this->getUser();
        $emailForm = $this->createForm(EmailModifyType::class);
        $passwordForm = $this->createForm(PasswordModifyType::class);

        $emailForm->handleRequest($request);
        $passwordForm->handleRequest($request);

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $user->setEmail($emailForm->getData()['mail']);
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                if ($e->getCode() === 1062) {
                    $emailForm->addError(new FormError('Email déjà utilisé.'));
                }
            }
        }

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $data = $passwordForm->getData();
            if ($hasher->isPasswordValid($user, $data['password'])) {
                $user->setPassword($hasher->hashPassword($user, $data['newPassword']));
                $this->entityManager->flush();
            } else {
                $passwordForm->addError(new FormError('Mot de passe incorrect.'));
            }
        }
        return [
            'emailForm' => $emailForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ];
    }

    private function setRepository(): void
    {
        if ($this->isGranted('ROLE_CANDIDAT')) {
            $this->repository = $this->entityManager->getRepository(ProfilCandidat::class);
        } elseif ($this->isGranted('ROLE_RECRUTEUR')) {
            $this->repository = $this->entityManager->getRepository(ProfilRecruteur::class);
        } else {
            $this->createAccessDeniedException();
        }
    }
}