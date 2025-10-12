<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/reports')]
class ReportsController extends AbstractController
{
    #[Route('/', name: 'app_reports_index')]
    public function index(CategoryRepository $categoryRepository, ProductsRepository $productsRepository): Response
    {
        $totalCategories = $categoryRepository->count([]);
        $totalProducts = $productsRepository->count([]);

        return $this->render('reports/index.html.twig', [
            'totalCategories' => $totalCategories,
            'totalProducts' => $totalProducts,
        ]);
    }
}
