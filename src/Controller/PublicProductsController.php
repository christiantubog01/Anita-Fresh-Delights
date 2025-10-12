<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PublicProductsController extends AbstractController
{
    #[Route('/all_products', name: 'app_all_products')]
    public function allProducts(ProductsRepository $productsRepository): Response
    {
        $products = $productsRepository->findAll();

        return $this->render('products/public_products.html.twig', [
            'products' => $products,
        ]);
    }
}
