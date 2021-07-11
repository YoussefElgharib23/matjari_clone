<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/products", name="app_product_")
 */
class ProductController extends AbstractController
{
    private ProductRepository $productRepository;

    private EntityManagerInterface $manager;

    public function __construct(
        ProductRepository $productRepository,
        EntityManagerInterface $manager
    ) {
        $this->productRepository = $productRepository;
        $this->manager = $manager;
    }

    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User $user */
        $user = $this->getUser();
        $panel = $user->getPanel();
        $products = $panel->getProducts();

        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"GET", "POST"})
     */
    public function create(Request $request)
    {
        $panel = $this->getUser()->getPanel();
        $product = (new Product())->setPanel($panel);
        $form = $this->createForm(ProductFormType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->manager->persist($product);

            $this->manager->flush();

            $this->addFlash('success', 'The product was created with success');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('products/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"}, requirements={"id": "\d+"})
     */
    public function edit(Product $product, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $product);

        $form = $this->createForm(ProductFormType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success', 'The product was edited with success');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('products/create.html.twig', [
            'form' => $form->createView(),
            'label' => 'Update',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function delete(Product $product, Request $request)
    {
        $this->denyAccessUnlessGranted('delete', $product);
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_product_id_'.$product->getId(), $token)) {
            return $this->redirectToRoute('app_product_index');
        }

        $this->manager->remove($product);
        $this->manager->flush();

        $this->addFlash('success', 'The product was deleted with success');

        return $this->redirectToRoute('app_product_index');
    }
}
