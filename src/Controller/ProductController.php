<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\PriceRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $em;
    private $productRepository;
    private $priceRepository;
    private $productCategoryRepository;

    public function __construct(EntityManagerInterface    $entityManager,
                                ProductRepository         $productRepository,
                                PriceRepository           $priceRepository,
                                ProductCategoryRepository $productCategoryRepository)
    {
        $this->em = $entityManager;
        $this->productRepository = $productRepository;
        $this->priceRepository = $priceRepository;
        $this->productCategoryRepository = $productCategoryRepository;
    }

    #[Route('/product', name: 'product_home')]
    public function indexAction(Request $request)
    {
        $listProducts = $this->productRepository->findAll();

        $listPrices = array();
        foreach ($listProducts as $product) {
            $savePrice = $this->priceRepository->findMaxPriceJoinedToProduct($product->getId());
            if ($savePrice !== null) {
                $listPrices[$product->getId()] = $savePrice;
            }
        }

        $listCategories = $this->productCategoryRepository->findAll();

        return $this->render('Product/index.html.twig', [
            'listProducts' => $listProducts,
            'listPrices' => $listPrices,
            'listCategories' => $listCategories,
        ]);
    }

    #[Route('/product/view/{id}', name: 'product_view')]
    public function viewAction($id)
    {
        $product = $this->productRepository->find($id);

        $listPrices = $this->priceRepository->findOrderedPricesJoinedToProduct($product->getId());

        return $this->render('Product/view.html.twig', [
            'product' => $product,
            'listPrices' => $listPrices,
            'listPricesInvert' => array_reverse($listPrices)
        ]);
    }

    #[Route('/product/add', name: 'product_add')]
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUpDate(new \Datetime());

            $this->em->persist($product);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistré.');

            return $this->redirect($this->generateUrl('product_view', array('id' => $product->getId())));
        }


        return $this->render('Product/add.html.twig', array(
            'form' => $form->createView(), 'product' => $product
        ));
    }

    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function editAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $product = $this->productRepository->find($id);
        if ($product == null) {
            throw $this->createNotFoundException("Le produit d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUpDate(new \Datetime());

            $this->em->persist($product);
            $this->em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistré.');

            return $this->redirect($this->generateUrl('product_view', array('id' => $product->getId())));
        }


        return $this->render('Product/edit.html.twig', array(
            'form' => $form->createView(), 'product' => $product
        ));
    }

    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function deleteAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $product = $this->productRepository->find($id);
        if (null === $product) {
            throw new NotFoundHttpException("Le produit d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepository->remove($product, true);

            $request->getSession()->getFlashBag()->add('info', "Le produit a bien été supprimée.");

            return $this->redirect($this->generateUrl('product_home'));
        }

        return $this->render('Product/delete.html.twig', array(
            'product' => $product,
            'form'   => $form->createView()
        ));

    }
}