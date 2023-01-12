<?php

namespace App\Controller\Consultant;

use App\Entity\CreateAccount;
use App\Repository\CreateAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @IsGranted("ROLE_CONSULTANT")
 * @Route("/consultant/creations-comptes")
 */
class CreateAccountController extends AbstractConsultantController
{
    /**
     * @param CreateAccountRepository $createAccountRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @Route("", name="consultant_create_index")
     */
    public function index(CreateAccountRepository $createAccountRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $createAccountsQuery = $createAccountRepository->findAllQuery();
        $entities = $paginator->paginate(
            $createAccountsQuery,
            $request->query->getInt('page', 1),
            10
        );

        $entities->setCustomParameters([
            'align' => 'center',
        ]);
        return $this->render('consultant/create.html.twig',[
            'entities' => $entities,
        ]);
    }

    /**
     * @param Request $request
     * @param CreateAccountRepository $repository
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/ajouter-compte", name="consultant_create_add", methods={"ADD"})
     */
    public function addAccount(Request $request, CreateAccountRepository $repository, EntityManagerInterface $entityManager): Response
    {
        /** @var ?CreateAccount $account */
        if ($account = $this->getAndCheckEntity('add', $request, $repository)) {
            $user = $account->getUser();
            if ($account->getRole() === 'ROLE_RECRUTEUR') {
                $user->setRoles(['ROLE_RECRUTEUR']);
            } else {
                $user->setRoles(['ROLE_CANDIDAT']);
            }
            try {
                $entityManager->remove($account);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute('consultant_create_index');
    }

    /**
     * @param Request $request
     * @param CreateAccountRepository $repository
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/supprimer-compte", name="consultant_create_delete", methods={"DELETE"})
     */
    public function deleteAccount(Request $request, CreateAccountRepository $repository, EntityManagerInterface $entityManager): Response
    {
        /** @var ?CreateAccount $createAccount */
        if ($createAccount = $this->getAndCheckEntity('delete', $request, $repository)) {
            $user = $createAccount->getUser();
            try {
                $entityManager->remove($createAccount);
                $entityManager->remove($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute('consultant_create_index');
    }
}