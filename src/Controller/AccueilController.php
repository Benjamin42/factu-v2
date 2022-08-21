<?php

namespace App\Controller;

use App\Repository\PriceRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{

    #[Route('/home', name: 'home')]
    public function indexAction(Request $request)
    {
        $number = random_int(0, 100);

        return $this->render('Accueil/index.html.twig', [
            'listCommande' => [],
            'listBdl' => [],
            'listClient' => [],
        ]);
    }
}