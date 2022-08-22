<?php

namespace App\Controller;

use App\DTO\CommandeDto;
use App\Repository\BdlRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatistiqueController extends AbstractController
{
    private $productRepository;
    private $productCategoryRepository;
    private $commandeRepository;
    private $bdlRepository;

    public function __construct(
        ProductRepository         $productRepository,
        ProductCategoryRepository $productCategoryRepository,
        CommandeRepository        $commandeRepository,
        BdlRepository             $bdlRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->commandeRepository = $commandeRepository;
        $this->bdlRepository = $bdlRepository;
    }

    #[Route('/stats/end_month', name: 'stat_end_month')]
    public function endMonthAction(Request $request)
    {
        $today = new \DateTime('tomorrow');
        $dateMonth = $today;

        $defaultsValues = array(
            'dateMonth' => $today->format('m/Y'),
        );

        $formFactory = Forms::createFormFactory();
        $form = $formFactory
            ->createBuilder(FormType::class, $defaultsValues)
            ->add('dateMonth', TextType::class, array('required' => false))
            ->add('search', SubmitType::class)
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest();

            if ($form['dateMonth']->getData() != null) {
                $dateMonthStr = $form['dateMonth']->getData();
                $dateMonth = \DateTime::createFromFormat('m/Y', $dateMonthStr);
            } else {
                $dateMonth = null;
            }
        }

        $listProducts = $this->productRepository->getFollowedStatProduct();

        $listCategories = $this->productCategoryRepository->getFollowedStatCategories();

        // Commandes
        $listCommandes = null;
        if ($dateMonth != null) {
            $month = date_format($dateMonth, 'm');
            $year = date_format($dateMonth, 'Y');

            $listCommandes = $this->commandeRepository->getCommandesByYearMonthDay($month, $year);
        } else {
            $listCommandes = $this->commandeRepository->findAll();
        }

        $listCommandeDto = array();
        $commandeTotalDto = new CommandeDto();
        foreach ($listCommandes as $commande) {
            if ($commande->getBdl() != null) {
                continue;
            }
            $commandeDto = new CommandeDto();

            $commandeDto->id = $commande->getId();
            $commandeDto->numFactu = $commande->getNumFactu();
            $commandeDto->dateFactu = $commande->getDateFactu();

            foreach ($commande->getCommandeProducts() as $commandeProduct) {
                if ($commandeProduct->getQty() != null && $commandeProduct->getQty() > 0) {
                    $commandeDto->addProduct($commandeProduct->getProduct(), $commandeProduct->getQty());
                    $commandeTotalDto->addProduct($commandeProduct->getProduct(), $commandeProduct->getQty());
                }
                if ($commandeProduct->getQtyGift() != null && $commandeProduct->getQtyGift() > 0) {
                    $commandeDto->addProduct($commandeProduct->getProduct(), $commandeProduct->getQtyGift());
                    $commandeTotalDto->addProduct($commandeProduct->getProduct(), $commandeProduct->getQtyGift());
                }
            }

            $listCommandeDto[] = $commandeDto;
        }


        // Bon de livraison
        $listBdls = null;
        if ($dateMonth != null) {
            $month = date_format($dateMonth, 'm');
            $year = date_format($dateMonth, 'Y');

            $listBdls = $this->bdlRepository->getBdlsByYearMonthDay($month, $year);
        } else {
            $listBdls = $this->bdlRepository->findAll();
        }

        foreach ($listBdls as $bdl) {
            $commandeDto = new CommandeDto();

            $commandeDto->id = $bdl->getId();
            $commandeDto->numBdl = $bdl->getNumBdl();
            $commandeDto->dateFactu = $bdl->getDateBdl();

            foreach ($bdl->getCommandeProducts() as $commandeProduct) {
                if ($commandeProduct->getQty() != null && $commandeProduct->getQty() > 0) {
                    $commandeDto->addProduct($commandeProduct->getProduct(), $commandeProduct->getQty());
                    $commandeTotalDto->addProduct($commandeProduct->getProduct(), $commandeProduct->getQty());
                }
                if ($commandeProduct->getQtyGift() != null && $commandeProduct->getQtyGift() > 0) {
                    $commandeDto->addProduct($commandeProduct->getProduct(), $commandeProduct->getQtyGift());
                    $commandeTotalDto->addProduct($commandeProduct->getProduct(), $commandeProduct->getQtyGift());
                }
            }
            $listCommandeDto[] = $commandeDto;
        }

        return $this->render('Statistique/end_month.html.twig', [
            'listProducts' => $listProducts,
            'listCategories' => $listCategories,
            'listCommandes' => $listCommandeDto,
            'commandeTotalDto' => $commandeTotalDto,
            'form' => $form->createView()
        ]);
    }

    #[Route('/stats/month_chart', name: 'stat_chart_month')]
    public function chartMonthAction(Request $request)
    {
        $listProducts = $this->productRepository->findAll();

        $listCommandes = $this->commandeRepository->findAllOrderByDateFactuDesc();

        $listCommandeDto = new ArrayCollection();
        $currentCommandeDto = null;
        $currentMonthDate = "";
        foreach ($listCommandes as $commande) {
            $monthDate = date_format($commande->getDateFactu(), 'Y-m');

            if ($currentCommandeDto == null || $currentMonthDate != $monthDate) {
                if ($currentCommandeDto != null) {
                    $listCommandeDto[] = $currentCommandeDto;
                }
                $commandeDto = new CommandeDto();
                $commandeDto->dateFactu = $commande->getDateFactu();
                $currentCommandeDto = $commandeDto;
                $currentMonthDate = $monthDate;
            }

            foreach ($commande->getCommandeProducts() as $commandeProduct) {
                if ($commandeProduct->getQty() != null && $commandeProduct->getQty() > 0) {
                    $commandeDto->addProduct($commandeProduct->getProduct(), $commandeProduct->getQty());
                }
            }

        }

        if ($currentCommandeDto != null) {
            $listCommandeDto[] = $currentCommandeDto;
        }

        return $this->render('Statistique/chart_month.html.twig', array(
            'listProducts' => $listProducts,
            'listCommandeDto' => $listCommandeDto
        ));
    }
}