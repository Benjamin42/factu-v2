<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Service;
use App\Form\CommandeType;
use App\Form\ServiceType;
use App\Repository\BdlRepository;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\ParameterRepository;
use App\Repository\PriceRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    private $em;
    private $commandeRepository;
    private $parameterRepository;
    private $priceRepository;
    private $clientRepository;
    private $bdlRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                CommandeRepository     $commandeRepository,
                                ParameterRepository    $parameterRepository,
                                PriceRepository        $priceRepository,
                                ClientRepository       $clientRepository,
                                BdlRepository          $bdlRepository
    )
    {
        $this->em = $entityManager;
        $this->commandeRepository = $commandeRepository;
        $this->parameterRepository = $parameterRepository;
        $this->priceRepository = $priceRepository;
        $this->clientRepository = $clientRepository;
        $this->bdlRepository = $bdlRepository;
    }

    #[Route('/commande', name: 'commande_home')]
    public function indexAction()
    {
        $listCommandes = $this->commandeRepository->findAll();

        return $this->render('Commande/index.html.twig', [
            'listCommandes' => $listCommandes
        ]);
    }

    #[Route('/commande/view/{id}', name: 'commande_view')]
    public function viewAction($id)
    {
        $commande = $this->commandeRepository->find($id);

        return $this->render('Commande/view.html.twig', [
            'commande' => $commande
        ]);
    }

    #[Route('/commande/view/factu/{id}', name: 'commande_view_factu')]
    public function viewFactuAction($id)
    {
        $commande = $this->commandeRepository->find($id);

        return $this->render('Commande/view_factu.html.twig', [
            'commande' => $commande,
            'parameterRepo' => $this->parameterRepository,
            'priceRepo' => $this->priceRepository
        ]);
    }

    #[Route('/commande/add', name: 'commande_add_no_id')]
    public function addAction(Request $request)
    {
        return $this->addActionWithId(null, $request);
    }

    #[Route('/commande/add/{id}', name: 'commande_add')]
    public function addActionWithId($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $commande = new Commande();

        if ($id) {
            $client = $this->clientRepository->find($id);
            if ($client != null) {
                $commande->setClient($client);
            }
        }

        $numFactu = $this->commandeRepository->getNextNumFactu();
        $commande->setNumFactu($numFactu);

        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($commande->getDateDelivered() == null) {
                $commande->setIsDelivered(False);
            } else {
                $commande->setIsDelivered(True);
            }

            $this->em->persist($commande);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Commande bien enregistrée.');

            // On met a jour le badge compteur de nombre de commande à livrer
            $nbCmdToDeliver = $this->commandeRepository->getNbCommandeToDelivery();
            $nbBdlToDeliver = $this->bdlRepository->getNbBdlToDelivery();
            $request->getSession()->set('nbCmdToDeliver', $nbCmdToDeliver + $nbBdlToDeliver);

            return $this->redirect($this->generateUrl('commande_view', array('id' => $commande->getId())));
        }


        return $this->render('Commande/add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route('/commande/edit/{id}', name: 'commande_edit')]
    public function editAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $commande = $this->commandeRepository->find($id);
        if ($commande == null) {
            throw $this->createNotFoundException("La commande d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($commande->getDateDelivered() == null) {
                $commande->setIsDelivered(False);
            } else {
                $commande->setIsDelivered(True);
            }

            $this->em->persist($commande);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Commande bien enregistrée.');

            // On met a jour le badge compteur de nombre de commande à livrer
            $nbCmdToDeliver = $this->commandeRepository->getNbCommandeToDelivery();
            $nbBdlToDeliver = $this->bdlRepository->getNbBdlToDelivery();
            $request->getSession()->set('nbCmdToDeliver', $nbCmdToDeliver + $nbBdlToDeliver);

            return $this->redirect($this->generateUrl('commande_view', array('id' => $commande->getId())));
        }

        return $this->render('Commande/edit.html.twig', array(
            'form' => $form->createView(),
            'commande' => $commande
        ));
    }

    #[Route('/commande/delete/{id}', name: 'commande_delete')]
    public function deleteAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $commande = $this->commandeRepository->find($id);
        if (null === $commande) {
            throw new NotFoundHttpException("La commande d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandeRepository->remove($commande, true);

            $request->getSession()->getFlashBag()->add('info', "Le service a bien été supprimée.");

            // On met a jour le badge compteur de nombre de commande à livrer
            $nbCmdToDeliver = $this->commandeRepository->getNbCommandeToDelivery();
            $nbBdlToDeliver = $this->bdlRepository->getNbBdlToDelivery();
            $request->getSession()->set('nbCmdToDeliver', $nbCmdToDeliver + $nbBdlToDeliver);

            return $this->redirect($this->generateUrl('commande_home'));
        }

        return $this->render('Commande/delete.html.twig', array(
            'commande' => $commande,
            'form' => $form->createView()
        ));

    }
}