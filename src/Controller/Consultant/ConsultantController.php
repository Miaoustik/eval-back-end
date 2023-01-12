<?php

namespace App\Controller\Consultant;

use App\Repository\AnnonceRepository;
use App\Repository\CandidatureRepository;
use App\Repository\CreateAccountRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/consultant")
 * @IsGranted("ROLE_CONSULTANT")
 */
class ConsultantController extends AbstractConsultantController
{

    /**
     * @param CreateAccountRepository $createAccountRepository
     * @param AnnonceRepository $annonceRepository
     * @param CandidatureRepository $candidatureRepository
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @Route("", name="consultant_index")
     */
    public function index(CreateAccountRepository $createAccountRepository, AnnonceRepository $annonceRepository, CandidatureRepository $candidatureRepository): Response
    {
        $countCreate = $createAccountRepository->countAll();
        $countAnnonce = $annonceRepository->countAll();
        $countCandidature = $candidatureRepository->countAll();

        return $this->render('consultant/index.html.twig',[
            'countCreate' => $countCreate,
            'countAnnonce' => $countAnnonce,
            'countCandidature' => $countCandidature,
        ]);
    }
}