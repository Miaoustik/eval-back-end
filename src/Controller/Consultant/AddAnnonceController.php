<?php

namespace App\Controller\Consultant;


use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/consultant/creation-annonce")
 * @IsGranted("ROLE_CONSULTANT")
 */
class AddAnnonceController extends AbstractConsultantController
{
    private EntityManagerInterface $manager;
    private AnnonceRepository $repository;

    public function __construct(AnnonceRepository $annonceRepository, EntityManagerInterface $manager)
    {
        $this->repository = $annonceRepository;
        $this->manager = $manager;
    }


    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("", name="consultant_annonce_index")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->repository->notAllowedQueryEager();
        $entities = $paginator->paginate(
           $query,
           $request->query->getInt('page', 1),
           10
       );
       $entities->setCustomParameters([
           'align' => 'center'
       ]);

       return $this->render('consultant/annonce.html.twig', [
           'entities' => $entities,
       ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/add", name="consultant_annonce_add", methods={"ADD"})
     */
    public function add(Request $request): Response
    {
        /** @var ?Annonce $annonce */
        if ($annonce = $this->getAndCheckEntity('add', $request, $this->repository)) {
            $annonce->setAllowed(true);
            try {
                $this->manager->flush();
            } catch (\Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }
        return $this->redirectToRoute('consultant_annonce_index');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/delete", name="consultant_annonce_delete", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        return $this->deleteEntity($request, $this->repository, $this->manager, "annonce");
    }
}