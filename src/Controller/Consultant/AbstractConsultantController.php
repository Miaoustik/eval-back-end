<?php

namespace App\Controller\Consultant;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractConsultantController extends AbstractController
{

    protected function addCsrfErrorFlash(): void
    {
        $this->addFlash('error', 'Token csrf invalide.');
    }

    protected function getAndCheckEntity($csrfKey, Request $request, EntityRepository $repository, $method = 'find')
    {
        $data = $request->request->all();
        if ( $this->isCsrfTokenValid($csrfKey . $data[$csrfKey], $data['_token'])) {
            if ( $entity = $repository->$method($data[$csrfKey])) {
                return $entity;
            } else {
                $this->addFlash("error", "L'annonce n'existe pas.");
            }
        } else {
            $this->addCsrfErrorFlash();
        }
        return null;
    }

    protected function deleteEntity($request, $repository, $manager, $path): Response
    {
        if ($entity = $this->getAndCheckEntity('delete', $request, $repository)) {
            try {
                $manager->remove($entity);
                $manager->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute("consultant_{$path}_index");
    }
}