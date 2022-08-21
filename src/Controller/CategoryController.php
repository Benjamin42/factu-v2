<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category_view')]
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