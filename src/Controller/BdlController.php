<?php

namespace App\Controller;

use App\DTO\BdlCommandeDto;
use App\Entity\Bdl;
use App\Entity\Service;
use App\Form\BdlType;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BdlController extends AbstractController
{
    private $em;
    private $bdlRepository;
    private $commandeRepository;
    private $parameterRepository;
    private $priceRepository;
    private $clientRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                BdlRepository          $bdlRepository,
                                CommandeRepository     $commandeRepository,
                                ParameterRepository    $parameterRepository,
                                PriceRepository        $priceRepository,
    ClientRepository $clientRepository)
    {
        $this->em = $entityManager;
        $this->bdlRepository = $bdlRepository;
        $this->commandeRepository = $commandeRepository;
        $this->parameterRepository = $parameterRepository;
        $this->priceRepository = $priceRepository;
        $this->clientRepository = $clientRepository;
    }

    #[Route('/bdl', name: 'bdl_home')]
    public function indexAction()
    {
        $listBdls = $this->bdlRepository->findAll();

        return $this->render('bdl/index.html.twig', [
            'listBdls' => $listBdls
        ]);
    }

    #[Route('/bdl/view/{id}', name: 'bdl_view')]
    public function viewAction($id)
    {
        $bdl = $this->bdlRepository->find($id);

        $listCommandes = $this->commandeRepository->findAllWithSpecificBdl($bdl);

        $listBdlDto = $this->calcStock($bdl);

        return $this->render('bdl/view.html.twig', [
            'bdl' => $bdl,
            'listBdlDto' => $listBdlDto,
            'listCommandes' => $listCommandes
        ]);
    }

    #[Route('/bdl/view/factu/{id}', name: 'bdl_view_factu')]
    public function viewFactuAction($id)
    {
        $bdl = $this->bdlRepository->find($id);

        return $this->render('bdl/view_factu.html.twig', [
            'bdl' => $bdl,
            'parameterRepo' => $this->parameterRepository,
            'priceRepo' => $this->priceRepository
        ]);
    }

    #[Route('/bdl/view_cmd_ajax', name: 'bdl_view_cmd_ajax')]
    public function viewCmdAjaxAction(Request $request) {
        $id = $request->query->get('id');
        $bdl = $this->bdlRepository->find($id);
        $listBdlDto = $this->calcStock($bdl);

        return $this->render('bdl/view_cmd.html.twig', [
            'listBdlDto' => $listBdlDto,
        ]);
    }

    private function calcStock($bdl)
    {
        $listBdlDto = array();
        foreach ($bdl->getCommandeProducts() as $commandeProduct) {
            $qty = $commandeProduct->getQty();
            $product = $commandeProduct->getProduct();
            if ($qty != null && $qty > 0) {
                $dto = new BdlCommandeDto($qty, $product);
                $listBdlDto[$product->getId()] = $dto;
            }
        }

        foreach ($bdl->getCommandes() as $commande) {
            foreach ($commande->getCommandeProducts() as $commandeProduct) {
                $qty = $commandeProduct->getQty();
                $product = $commandeProduct->getProduct();
                if ($qty != null && $qty > 0) {
                    $dto = $listBdlDto[$product->getId()];
                    $dto->add($qty);
                }
            }
        }

        return $listBdlDto;
    }

    #[Route('/bdl/view_data_ajax', name: 'bdl_view_data_ajax')]
    public function viewDataAjaxAction(Request $request) {
        $id = $request->query->get('id');
        $bdl = $this->bdlRepository->find($id);

        $response = array("success" => true, "dateBdl" => $bdl->getDateBdl()->format('d/m/Y'));

        return new Response(json_encode($response));
    }

    #[Route('/bdl/add', name: 'bdl_add_no_id')]
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->addActionWithId(null, $request);
    }

    #[Route('/bdl/add/{id}', name: 'bdl_add')]
    public function addActionWithId($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $bdl = new Bdl();

        if ($id) {
            $client = $this->clientRepository->find($id);
            if ($client != null) {
                $bdl->setClient($client);
            }
        }

        // Init du numéro de bdl en prenant le max + 1
        $numFactu = $this->bdlRepository->getNextNumBdl();
        $bdl->setNumBdl($numFactu);

        $form = $this->createForm(BdlType::class, $bdl);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($bdl->getDateDelivered() == null) {
                $bdl->setIsDelivered(False);
            } else {
                $bdl->setIsDelivered(True);
            }

            $this->em->persist($bdl);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Bon de livraison bien enregistré.');

            // On met a jour le badge compteur de nombre de commande à livrer
            $nbCmdToDeliver = $this->commandeRepository->getNbCommandeToDelivery();
            $nbBdlToDeliver = $this->bdlRepository->getNbBdlToDelivery();
            $request->getSession()->set('nbCmdToDeliver', $nbCmdToDeliver + $nbBdlToDeliver);

            return $this->redirect($this->generateUrl('bdl_view', array('id' => $bdl->getId())));
        }


        return $this->render('bdl/add.html.twig', array(
            'form' => $form->createView(),
            'bdl' => $bdl
        ));
    }

    #[Route('/bdl/edit/{id}', name: 'bdl_edit')]
    public function editAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $bdl = $this->bdlRepository->find($id);
        if ($bdl == null) {
            throw $this->createNotFoundException("Le bon de livraison d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(BdlType::class, $bdl);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($bdl->getDateDelivered() == null) {
                $bdl->setIsDelivered(False);
            } else {
                $bdl->setIsDelivered(True);
            }

            $this->em->persist($bdl);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Bon de livraison bien enregistré.');

            // On met a jour le badge compteur de nombre de commande à livrer
            $nbCmdToDeliver = $this->commandeRepository->getNbCommandeToDelivery();
            $nbBdlToDeliver = $this->bdlRepository->getNbBdlToDelivery();
            $request->getSession()->set('nbCmdToDeliver', $nbCmdToDeliver + $nbBdlToDeliver);

            return $this->redirect($this->generateUrl('bdl_view', array('id' => $bdl->getId())));
        }

        return $this->render('bdl/edit.html.twig', array(
            'form' => $form->createView(),
            'bdl' => $bdl
        ));
    }

    #[Route('/bdl/delete/{id}', name: 'bdl_delete')]
    public function deleteAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $bdl = $this->bdlRepository->find($id);
        if (null === $bdl) {
            throw new NotFoundHttpException("Le bon de livraison d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(BdlType::class, $bdl);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->bdlRepository->remove($bdl, true);

            $request->getSession()->getFlashBag()->add('info', "Le bon de livraison a bien été supprimée.");

            // On met a jour le badge compteur de nombre de commande à livrer
            $nbCmdToDeliver = $this->commandeRepository->getNbCommandeToDelivery();
            $nbBdlToDeliver = $this->bdlRepository->getNbBdlToDelivery();
            $request->getSession()->set('nbCmdToDeliver', $nbCmdToDeliver + $nbBdlToDeliver);


            return $this->redirect($this->generateUrl('bdl_home'));
        }

        return $this->render('bdl/delete.html.twig', array(
            'bdl' => $bdl,
            'form'   => $form->createView()
        ));

    }

}