<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ParameterType;
use App\Form\ServiceType;
use App\Repository\ParameterRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ParameterController extends AbstractController
{
    private $em;
    private $parameterRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                ParameterRepository    $parameterRepository)
    {
        $this->em = $entityManager;
        $this->parameterRepository = $parameterRepository;
    }

    #[Route('/parameter', name: 'parameter_home')]
    public function indexAction()
    {
        $listParameters = $this->parameterRepository->findAll();

        return $this->render('parameter/index.html.twig', [
            'listParameters' => $listParameters
        ]);
    }

    #[Route('/parameter/edit/{id}', name: 'parameter_edit')]
    public function editAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $parameter = $this->parameterRepository->find($id);
        if ($parameter == null) {
            throw $this->createNotFoundException("Le parametre d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(ParameterType::class, $parameter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($parameter);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Parametre bien enregistré.');

            return $this->redirect($this->generateUrl('parameter_home'));
        }

        return $this->render('parameter/edit.html.twig', array(
            'form' => $form->createView(), 'parameter' => $parameter
        ));
    }

}