<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    private $em;
    private $serviceRepository;

    public function __construct(EntityManagerInterface    $entityManager,
                                ServiceRepository         $serviceRepository)
    {
        $this->em = $entityManager;
        $this->serviceRepository = $serviceRepository;
    }

    #[Route('/service', name: 'service_home')]
    public function indexAction()
    {
        $listServices = $this->serviceRepository->findAll();

        return $this->render('Service/index.html.twig', [
            'listServices' => $listServices
        ]);
    }

    #[Route('/service/view/{id}', name: 'service_view')]
    public function viewAction($id)
    {
        $service = $this->serviceRepository->find($id);

        return $this->render('Service/view.html.twig', [
            'service' => $service
        ]);
    }

    #[Route('/service/add', name: 'service_add')]
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = new Service();

        $form = $this->createForm(ServiceType::class, $service);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->setUpDate(new \Datetime());

            $this->em->persist($service);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Service bien enregistré.');

            return $this->redirect($this->generateUrl('service_view', array('id' => $service->getId())));
        }


        return $this->render('Service/add.html.twig', array(
            'form' => $form->createView(), 'service' => $service
        ));
    }

    #[Route('/service/edit/{id}', name: 'service_edit')]
    public function editAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->serviceRepository->find($id);
        if ($service == null) {
            throw $this->createNotFoundException("Le service d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->setUpDate(new \Datetime());

            $this->em->persist($service);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Service bien enregistré.');

            return $this->redirect($this->generateUrl('service_view', array('id' => $service->getId())));
        }


        return $this->render('Service/edit.html.twig', array(
            'form' => $form->createView(), 'service' => $service
        ));
    }

    #[Route('/service/delete/{id}', name: 'service_delete')]
    public function deleteAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->serviceRepository->find($id);
        if (null === $service) {
            throw new NotFoundHttpException("Le service d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->serviceRepository->remove($service, true);

            $request->getSession()->getFlashBag()->add('info', "Le service a bien été supprimée.");

            return $this->redirect($this->generateUrl('service_home'));
        }

        return $this->render('Service/delete.html.twig', array(
            'service' => $service,
            'form'   => $form->createView()
        ));

    }
}