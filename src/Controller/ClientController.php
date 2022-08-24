<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Service;
use App\Form\ClientType;
use App\Form\ServiceType;
use App\Repository\ClientRepository;
use App\Repository\CountryRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    private $em;
    private $clientRepository;
    private $countryRepository;

    public function __construct(EntityManagerInterface    $entityManager,
                                ClientRepository         $clientRepository,
    CountryRepository $countryRepository)
    {
        $this->em = $entityManager;
        $this->clientRepository = $clientRepository;
        $this->countryRepository = $countryRepository;
    }

    #[Route('/client', name: 'client_home')]
    public function indexAction()
    {
        $listClients = $this->clientRepository->findAll();

        return $this->render('client/index.html.twig', [
            'listClients' => $listClients
        ]);
    }

    #[Route('/client/view/{id}', name: 'client_view')]
    public function viewAction($id)
    {
        $client = $this->clientRepository->find($id);

        return $this->render('client/view.html.twig', [
            'client' => $client
        ]);
    }

    #[Route('/client/add', name: 'client_add')]
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $client = new Client();
        $france = $this->countryRepository->findOneBy(["code" => "FR"]);
        if ($france) {
            $client->setPays($france);
        }

        $numClient = $this->clientRepository->getNextNumClient();
        $client->setNumClient($numClient);

        $form = $this->createForm(ClientType::class, $client);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($client);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Client bien enregistré.');

            return $this->redirect($this->generateUrl('client_view', array('id' => $client->getId())));
        }

        return $this->render('client/add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route('/client/edit/{id}', name: 'client_edit')]
    public function editAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $client = $this->clientRepository->find($id);
        if ($client == null) {
            throw $this->createNotFoundException("Le client d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($client);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Client bien enregistré.');

            return $this->redirect($this->generateUrl('client_view', array('id' => $client->getId())));
        }

        return $this->render('client/edit.html.twig', array(
            'form' => $form->createView(), 'client' => $client
        ));
    }

    #[Route('/client/delete/{id}', name: 'client_delete')]
    public function deleteAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $client = $this->clientRepository->find($id);
        if (null === $client) {
            throw new NotFoundHttpException("Le client d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->clientRepository->remove($client, true);

            $request->getSession()->getFlashBag()->add('info', "Le client a bien été supprimée.");

            return $this->redirect($this->generateUrl('client_home'));
        }

        return $this->render('client/delete.html.twig', array(
            'client' => $client,
            'form'   => $form->createView()
        ));

    }
}