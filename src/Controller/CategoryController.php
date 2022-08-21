<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\ProductCategoryType;
use App\Repository\PriceRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $em;
    private $productCategoryRepository;

    public function __construct(EntityManagerInterface    $entityManager,
                                ProductCategoryRepository $productCategoryRepository)
    {
        $this->em = $entityManager;
        $this->productCategoryRepository = $productCategoryRepository;
    }

    #[Route('/category/view/{id}', name: 'category_view')]
    public function viewAction($id)
    {
        $category = $this->productCategoryRepository->find($id);

        return $this->render('Category/view.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/category/add', name: 'category_add')]
    public function addAction(Request $request)
    {
        $cat = new ProductCategory();

        $form = $this->createForm(ProductCategoryType::class, $cat);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($cat);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Catégorie bien enregistré.');

            return $this->redirect($this->generateUrl('category_view', array('id' => $cat->getId())));
        }

        return $this->render('Category/add.html.twig', array(
            'form' => $form->createView(),
            'category' => $cat
        ));
    }

    #[Route('/category/edit/{id}', name: 'category_edit')]
    public function editAction($id, Request $request)
    {
        $cat = $this->productCategoryRepository->find($id);
        if ($cat == null) {
            throw $this->createNotFoundException("La catégorie d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(ProductCategoryType::class, $cat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($cat);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Catégorie bien enregistré.');

            return $this->redirect($this->generateUrl('category_view', array('id' => $cat->getId())));
        }

        return $this->render('Category/edit.html.twig', array(
            'form' => $form->createView(),
            'category' => $cat
        ));
    }

    #[Route('/category/delete/{id}', name: 'category_delete')]
    public function deleteAction($id, Request $request)
    {
        $cat = $this->productCategoryRepository->find($id);
        if (null === $cat) {
            throw new NotFoundHttpException("La catégorie d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(ProductCategoryType::class, $cat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productCategoryRepository->remove($cat, true);

            $request->getSession()->getFlashBag()->add('info', "La catégorie a bien été supprimée.");

            return $this->redirect($this->generateUrl('product_home'));
        }

        return $this->render('Category/delete.html.twig', array(
            'category' => $cat,
            'form'   => $form->createView()
        ));
    }
}