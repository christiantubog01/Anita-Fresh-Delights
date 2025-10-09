<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface; // Added for slugging filenames
use Symfony\Component\HttpFoundation\File\Exception\FileException; // Added for handling file upload exceptions

#[Route('/dashboard/products')]
final class ProductsController extends AbstractController
{
    #[Route(name: 'app_products_index', methods: ['GET'])]
    public function index(ProductsRepository $productsRepository): Response
    {
        return $this->render('products/index.html.twig', [
            'products' => $productsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_products_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $product = new Products();
    $form = $this->createForm(ProductsType::class, $product);
    $form->handleRequest($request);
//Image Input
    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/products',
                    $newFilename
                );
            } catch (FileException $e) {
                
                $this->addFlash('error', 'Image upload failed: ' . $e->getMessage());
            }

            $product->setImage($newFilename);
        }

        $entityManager->persist($product);
        $entityManager->flush();
//Image Input
        return $this->redirectToRoute('app_products_index');
    }

    return $this->render('products/new.html.twig', [
        'product' => $product,
        'form' => $form->createView(),
    ]);
}


    #[Route('/{id}', name: 'app_products_show', methods: ['GET'])]
    public function show(Products $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

   #[Route('/{id}/edit', name: 'app_products_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Products $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $form = $this->createForm(ProductsType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/products',
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'File upload failed: ' . $e->getMessage());
            }

            // Update image only if new one uploaded
            $product->setImage($newFilename);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Product updated successfully!');
        return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
    }

        return $this->render('products/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(), //edited "->createView(),"
        ]);
    }

    #[Route('/{id}', name: 'app_products_delete', methods: ['POST'])]
    public function delete(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
    }
}
