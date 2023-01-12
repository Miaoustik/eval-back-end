<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Candidature;
use App\Repository\AnnonceRepository;
use App\Repository\CandidatureRepository;
use App\Repository\ProfilCandidatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/candidat")
 * @IsGranted("ROLE_CANDIDAT")
 */
class CandidatController extends AbstractController
{

    /**
     * @param ProfilCandidatRepository $profilCandidatRepository
     * @param CandidatureRepository $candidatureRepository
     * @return Response
     * @Route("", name="candidat_index")
     */
    public function index(ProfilCandidatRepository $profilCandidatRepository, CandidatureRepository $candidatureRepository): Response
    {
        $profil = $profilCandidatRepository->findOneByUser($this->getUser());

        $candidatures = $candidatureRepository->findByProfilCandidat($profil);

        return $this->render('candidat/index.html.twig', [
            'profil' => $profil,
            'candidatures' => $candidatures,
        ]);
    }

    /**
     * @param ProfilCandidatRepository $profilCandidatRepository
     * @return Response
     * @Route("/cv", name="candidat_cv")
     */
    public function cvView(ProfilCandidatRepository $profilCandidatRepository): Response
    {
        $profilCandidat = $profilCandidatRepository->findOneByUser($this->getUser());

        return $this->file($this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cv' . DIRECTORY_SEPARATOR . $profilCandidat->getCvName(), null, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @param $id
     * @param ProfilCandidatRepository $profilCandidatRepository
     * @param CandidatureRepository $candidatureRepository
     * @param AnnonceRepository $annonceRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/postuler/{id}", name="candidat_postuler", requirements={"id"="\d++"}, methods={"POST"})
     * @throws NonUniqueResultException
     */
    public function postuler($id, ProfilCandidatRepository $profilCandidatRepository, CandidatureRepository $candidatureRepository, AnnonceRepository $annonceRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('postuler' . $id, $request->request->get('postuler'))) {
            $user = $this->getUser();
            $annonce = $annonceRepository->findEager($id);

            $profil = $profilCandidatRepository->findOneByUser($user);
            if ($profil === null) {
                $this->addFlash('profil', 'Vous devez compléter votre profil avant de pouvoir postuler.');
                return $this->redirectToRoute('profil_modify');
            }

            $candidatures = $annonce->getCandidatures();

            foreach ($candidatures as $candidature) {
                if ($candidature->getProfilCandidat()->getId() === $profil->getId()) {
                    $this->addFlash('error', 'Vous avez déjà postuler à cette annonce.');
                    return $this->redirectToRoute('annonce_show', ['id' => $id]);
                }
            }

            $candidature = new Candidature($profil, $annonce);
            $entityManager->persist($candidature);

            try {
                $entityManager->flush();
                $this->addFlash('success', "Votre candidature a bien été enregistrée");
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur avec la candidature.');
            }
        }
        return $this->redirectToRoute('annonce_show', ['id' => $id]);
    }
}