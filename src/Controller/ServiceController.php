<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/service', name: 'service_home')]
    public function indexAction(Request $request)
    {
        $number = random_int(0, 100);

        return $this->render('accueil/index.html.twig', [
            'listCommande' => [],
            'listBdl' => [],
            'listClient' => [],
        ]);
    }
}