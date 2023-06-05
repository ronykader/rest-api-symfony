<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/products', name: 'product_index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $products = $doctrine->getRepository(Product::class)->findAll();
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'productName' => $product->getProductName(),
                'productCategory' => $product->getCategoryId(),
                'productBrand' => $product->getBrandId(),
                'minimumQuantity' => $product->getMinimumQuantity(),
                'quantity' => $product->getQuantity(),
                'price' => $product->getPrice()
            ];

        }
        return $this->json($data, Response::HTTP_OK);
    }


    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/products', name: 'product_create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $product = new Product();
        $this->createUpdateProcess($product, $request);
        $entityManager->persist($product);
        $entityManager->flush();

        $data = [
            'id' => $product->getId(),
            'productName' => $product->getProductName(),
            'productCategory' => $product->getCategoryId(),
            'productBrand' => $product->getBrandId(),
            'minimumQuantity' => $product->getMinimumQuantity(),
            'quantity' => $product->getQuantity(),
            'price' => $product->getPrice()
        ];

        return $this->json($data, Response::HTTP_CREATED);
    }


    /**
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/products/{id}', name: 'product_details', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $product = $doctrine->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->json('No product found for id ' . $id, Response::HTTP_NOT_FOUND);
        }
        $data = [
            'id' => $product->getId(),
            'productName' => $product->getProductName(),
            'productCategory' => $product->getCategoryId(),
            'productBrand' => $product->getBrandId(),
            'minimumQuantity' => $product->getMinimumQuantity(),
            'quantity' => $product->getQuantity(),
            'price' => $product->getPrice()
        ];

        return $this->json($data);
    }


    #[Route('/products/{id}', name: 'product_update', methods: ['PUT', 'PATCH'])]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->json('No product found for id ' . $id, Response::HTTP_NOT_FOUND);
        }
        $this->createUpdateProcess($product, $request);
        $entityManager->flush();
        $data = [
            'id' => $product->getId(),
            'productName' => $product->getProductName(),
            'productCategory' => $product->getCategoryId(),
            'productBrand' => $product->getBrandId(),
            'minimumQuantity' => $product->getMinimumQuantity(),
            'quantity' => $product->getQuantity(),
            'price' => $product->getPrice()
        ];

        return $this->json($data, Response::HTTP_OK);

    }


    #[Route('/products/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function destroy(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->json('No product found for id ' . $id, Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json('Deleted a product successfully with id ' . $id, Response::HTTP_OK);
    }

    /**
     * @param Product $product
     * @param Request $request
     * @return void
     */
    public function createUpdateProcess(Product $product, Request $request): void
    {
        $product->setProductName($request->request->get('product_name'));
        $product->setCategoryId($request->request->get('category_id'));
        $product->setBrandId($request->request->get('brand_id'));
        $product->setMinimumQuantity($request->request->get('minimum_quantity'));
        $product->setPrice($request->request->get('price'));
        $product->setUnitType($request->request->get('unit_price'));
        $product->setQuantity($request->request->get('quantity'));
        $product->setStatus($request->request->get('status'));
    }
}
