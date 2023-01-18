<?php

namespace App\Controller\Consultant;

use App\Entity\Candidature;
use App\Entity\ProfilCandidat;
use App\Repository\CandidatureRepository;
use App\Repository\ProfilCandidatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/consultant/autoriser-candidature")
 */
class AddCandidatureController extends AbstractConsultantController
{

    private CandidatureRepository $repository;
    private EntityManagerInterface $manager;

    public function __construct(CandidatureRepository $repository, EntityManagerInterface $manager)
    {

        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("", name="consultant_candidature_index")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->repository->notAllowedQuery();
        $entities = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10,

        );

        $entities->setCustomParameters([
            'align' => 'center'
        ]);

        return $this->render('consultant/candidature.html.twig', [
            'entities' => $entities
        ]);
    }

    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @param ProfilCandidatRepository $profilCandidatRepository
     * @return Response
     * @Route("/add", name="consultant_candidature_add", methods={"ADD"})
     */
    public function add(Request $request, MailerInterface $mailer, ProfilCandidatRepository $profilCandidatRepository): Response
    {
        /** @var Candidature $candidature */
        if ($candidature = $this->getAndCheckEntity('add', $request, $this->repository, 'findEager')) {

            $path = $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cv' . DIRECTORY_SEPARATOR;

            /** @var ProfilCandidat $profilCandidat */
            $profilCandidat = $candidature->getProfilCandidat();
            $annonce = $candidature->getAnnonce();
            $profilRecruteur = $annonce->getProfilRecruteur();
            $emailRecruteur = $profilRecruteur->getUser()->getEmail();

            $candidature->setAllowed(true);
            $appEmail = $this->getParameter('app.email');

            $mail = (new TemplatedEmail())
                ->from(new Address($appEmail, 'Trt-Conseil'))
                ->to(new Address($emailRecruteur, $profilRecruteur->getSocietyName()))
                ->subject('TRT-Conseil : Candidature pour votre annonce ' . $annonce->getTitle() . '.')
                ->htmlTemplate('email/candidature.html.twig')
                ->context([
                    'annonce' => $annonce,
                    'profilCandidat' => $profilCandidat,
                    'profilRecruteur' => $profilRecruteur,
                ])
                ->attachFromPath($path . $profilCandidat->getCvName(), 'Cv-'. $profilCandidat->getLastname() . '-' . $profilCandidat->getFirstname() . '.pdf');
            $mail->getHeaders()
                ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');

            try {
                $this->manager->flush();
                $mailer->send($mail);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute('consultant_candidature_index');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/delete", name="consultant_candidature_delete")
     */
    public function delete(Request $request): Response
    {
        /** @var ?Candidature $candidature */
        if ($candidature = $this->getAndCheckEntity('delete', $request, $this->repository, 'findForDelete')) {
            $profil = $candidature->getProfilCandidat();
            $annonce = $candidature->getAnnonce();
            try {
                $profil->removeCandidature($candidature);
                $annonce->removeCandidature($candidature);
                $this->manager->remove($candidature);
                $this->manager->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute("consultant_candidature_index");
    }
}