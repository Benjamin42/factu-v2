<?php

namespace App\Controller;

use App\Repository\BdlRepository;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\PriceRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    private $clientRepository;
    private $commandeRepository;
    private $bdlRepository;

    public function __construct(ClientRepository $clientRepository,
                                CommandeRepository     $commandeRepository,
                                BdlRepository          $bdlRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->commandeRepository = $commandeRepository;
        $this->bdlRepository = $bdlRepository;
    }

    #[Route('/', name: 'home')]
    public function indexAction(Request $request)
    {
        $nbCmdToDeliver = $this->commandeRepository->getNbCommandeToDelivery();
        $request->getSession()->set('nbCmdToDeliver', $nbCmdToDeliver);

        $listClient = $this->clientRepository->getLastAdded();
        $listBdl = $this->bdlRepository->getLastAdded();
        $listCommande = $this->commandeRepository->getLastAdded();

        return $this->render('Accueil/index.html.twig', [
            "listClient" => $listClient,
            "listBdl" => $listBdl,
            "listCommande" => $listCommande,
        ]);
    }
}