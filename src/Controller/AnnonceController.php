<?php

namespace App\Controller;

use App\Form\AnnonceSearchType;
use App\Repository\AnnonceRepository;
use App\Repository\CandidatureRepository;
use App\Repository\ProfilCandidatRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/annonces")
 */
class AnnonceController extends AbstractController
{

    /**
     * @param PaginatorInterface $paginator
     * @param AnnonceRepository $annonceRepository
     * @param Request $request
     * @return Response
     * @Route("", name="annonce_index")
     */
    public function index(PaginatorInterface $paginator, AnnonceRepository $annonceRepository, Request $request): Response
    {
        $search = $this->createForm(AnnonceSearchType::class);
        $search->handleRequest($request);

        if ($search->isSubmitted() && $search->isValid()) {
            $query = $annonceRepository->findBySearch($search->getData());
        } else {
            $query = $annonceRepository->findAllAllowedQuery();
        }

        $annonces = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        $annonces->setCustomParameters([
            'align' => 'center',
        ]);
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
            'search' => $search->createView(),
        ]);
    }

    /**
     * @param $id
     * @param AnnonceRepository $annonceRepository
     * @param ProfilCandidatRepository $profilCandidatRepository
     * @param CandidatureRepository $candidatureRepository
     * @return Response
     * @Route("/annonce-{id}", requirements={"id"="\d++"}, name="annonce_show")
     */
    public function show($id, AnnonceRepository $annonceRepository, ProfilCandidatRepository $profilCandidatRepository,CandidatureRepository $candidatureRepository): Response
    {

        $annonce = $annonceRepository->findEagerShow($id);
        $candidatures = $annonce->getCandidatures();

        if ($this->isGranted('ROLE_CANDIDAT')) {
            $profil = $profilCandidatRepository->findOneByUser($this->getUser());
            $postuler = true;
            foreach ($candidatures as $candidature) {
                if ($candidature->getProfilCandidat()->getId() === $profil->getId()) {
                    $postuler = false;
                }
            }
        } elseif ($this->isGranted('ROLE_RECRUTEUR') && $annonce->getProfilRecruteur()->getUser()->getId() === $this->getUser()->getId()) {
            $candidaturesRecruteur = $candidatureRepository->findAllowedEagerByAnnonce($annonce);
        }

        $candidaturesCount = $candidatures->filter(function ($e) {
            return $e->isAllowed();
        });

        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
            'postuler' => $postuler ?? null,
            'candidatures' => $candidaturesCount,
            'candidaturesRecruteur' => $candidaturesRecruteur ?? null,
        ]);
    }
}