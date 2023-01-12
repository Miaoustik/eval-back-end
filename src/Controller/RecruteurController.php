<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use App\Repository\ProfilCandidatRepository;
use App\Repository\ProfilRecruteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recruteur")
 * @IsGranted("ROLE_RECRUTEUR")
 */
class RecruteurController extends AbstractController
{
    /**
     * @param ProfilRecruteurRepository $profilRecruteurRepository
     * @param AnnonceRepository $annonceRepository
     * @return Response
     * @Route("", name="recruteur_index")
     */
    public function index(ProfilRecruteurRepository $profilRecruteurRepository, AnnonceRepository $annonceRepository): Response
    {
        $user = $this->getUser();
        $profil = $profilRecruteurRepository->findOneByUser($user);
        $annonces = $annonceRepository->findByProfilRecruteur($profil);
        $candidatureCount = [];
        foreach ($annonces as $annonce) {
            /** @var Annonce $annonce */
            $candidatures = $annonce->getCandidatures();
            $annonceId = $annonce->getId();
            foreach ($candidatures as $candidature) {
                if ($candidature->isAllowed() === true) {
                    if (isset($candidatureCount[$annonceId])) {
                        $candidatureCount[$annonceId] += 1;
                    } else {
                        $candidatureCount[$annonceId] = 1;
                    }
                }
            }
            if (!isset($candidatureCount[$annonceId])) {
                $candidatureCount[$annonceId] = 0;
            }
        }

        return $this->render('recruteur/index.html.twig', [
            'annonces' => $annonces,
            'profil' => $profil,
            'candidaturesCount' => $candidatureCount,
        ]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param ProfilRecruteurRepository $profilRecruteurRepository
     * @param Request $request
     * @return Response
     * @Route("/creer-annonce", name="recruteur_create")
     */
    public function createAnnonce(EntityManagerInterface $manager, ProfilRecruteurRepository $profilRecruteurRepository, Request $request): Response
    {
        $user = $this->getUser();
        $profil = $profilRecruteurRepository->findOneByUser($user);
        if ($profil === null) {
            $this->addFlash('profil', 'Vous devez compléter votre profil avant de pouvoir créer une annonce.');
            return $this->redirectToRoute('profil_modify');
        }

        $form = $this->createForm(AnnonceType::class, new Annonce());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Annonce $annonce */
            $annonce = $form->getData();
            $annonce->setAllowed(false)
                ->setProfilRecruteur($profil);
            $manager->persist($annonce);
            try {
                $manager->flush();
                $this->addFlash('success', 'L\'annonce a bien été crée.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur avec la création d\'annonces');
            }
            return $this->redirectToRoute('recruteur_index');
        }

        return $this->render('recruteur/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $id
     * @param ProfilCandidatRepository $profilCandidatRepository
     * @param Request $request
     * @return Response
     * @Route("/candidature/cv/{id}", name="recruteur_cv", methods={"POST"}, requirements={"id"="\d++"})
     */
    public function getCv(int $id, ProfilCandidatRepository $profilCandidatRepository, Request $request): Response
    {
        if ($this->isCsrfTokenValid('cv' . $id, $request->request->get('token'))) {
            $profil = $profilCandidatRepository->find($id);
            return $this->file($this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cv' . DIRECTORY_SEPARATOR . $profil->getCvName(), null, ResponseHeaderBag::DISPOSITION_INLINE);
        } else {
            $this->addFlash('error', 'csrf token invalid.');
            return $this->redirectToRoute('home_index');
        }
    }
}