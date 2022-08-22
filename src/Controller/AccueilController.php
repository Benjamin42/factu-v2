<?php

namespace App\Controller;

use App\Repository\ClientRepository;
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

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    #[Route('/', name: 'home')]
    public function indexAction(Request $request)
    {

        $listClient = $this->clientRepository->getLastAdded();

        return $this->render('Accueil/index.html.twig', [
            'listCommande' => [],
            'listBdl' => [],
            'listClient' => $listClient
        ]);
    }
}