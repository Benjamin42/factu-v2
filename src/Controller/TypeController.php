<?php

namespace App\Controller;

use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TypeController extends AbstractController
{
    private $em;
    private $typeRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                TypeRepository    $typeRepository)
    {
        $this->em = $entityManager;
        $this->typeRepository = $typeRepository;
    }

    #[Route('/type', name: 'type_home')]
    public function indexAction()
    {
        $listTypes = $this->typeRepository->findAll();

        return $this->render('Type/index.html.twig', array(
            'listTypes' => $listTypes
        ));
    }
}