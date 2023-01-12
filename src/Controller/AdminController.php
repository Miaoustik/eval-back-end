<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateConsultantType;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     * @Route("", name="admin_index")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $consultantCleanForm = $this->createForm(CreateConsultantType::class);
        $consultantForm = $consultantCleanForm->handleRequest($request);

        if ($consultantForm->isSubmitted() && $consultantForm->isValid()) {
            $data = $consultantForm->getData();
            $user = new User();
            $user->setEmail($data['email'])
                ->setPassword($hasher->hashPassword($user, $data['password']))
                ->setRoles(['ROLE_CONSULTANT']);

            $entityManager->persist($user);

            try{
                $entityManager->flush();
                $this->addFlash('success', 'Le consultant a bien été créé.');
            } catch (Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('admin/index.html.twig', [
            'consultantForm' => $consultantCleanForm->createView(),
        ]);
    }
}