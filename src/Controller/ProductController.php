<?php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{


    #[Route('/products', name: 'products_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Product::class)->findAll();

        return $this->render('product/products.html.twig', [
            'products' => $products,
        ]);
    }


    #[Route('/product/new', name: 'product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductTypeForm::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On assigne l'utilisateur connecté comme propriétaire du produit
            $product->setUser($this->getUser());

            $em->persist($product);
            $em->flush();

            $this->addFlash('browser', 'Produit crée avec succès !');

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('product/new.html.twig', [
            'productForm' => $form->createView(),
        ]);
    }

    #[Route('/product/{id}/edit', name: 'product_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $product = $em->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Produit #$id non trouvé");
        }

        $form = $this->createForm(ProductTypeForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('browser', 'Produit modifié avec succès !');

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('product/edit.html.twig', [
            'productForm' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/delete', name: 'product_delete', methods: ['GET', 'POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $product = $em->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Produit #$id non trouvé");
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();

            $this->addFlash('browser', "Produit #$id supprimé avec succès !");
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}