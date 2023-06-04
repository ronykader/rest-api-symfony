<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'category_api_')]
class CategoryController extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/category', name: 'category_list', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $categories = $doctrine->getRepository(Category::class)->findAll();
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'categoryName' => $category->getName(),
                'slug' => $category->getSlug(),
                'status' => $category->getStatus()
            ];
        }
        return $this->json($data, Response::HTTP_OK);
    }


    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/category', name: 'category_create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $category = new Category();
        $category->setName($request->request->get('name'));
        $category->setSlug($request->request->get('slug'));
        $category->setStatus($request->request->get('status'));
        $entityManager->persist($category);
        $entityManager->flush();

        $data = [
            'id' => $category->getId(),
            'categoryName' => $category->getName(),
            'slug' => $category->getSlug(),
            'status' => $category->getStatus()
        ];

        return $this->json($data, Response::HTTP_CREATED);
    }


    /**
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/category/{id}', name: 'category_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $category = $doctrine->getRepository(Category::class)->find($id);
        if (!$category) {
            return $this->json('Category data not found for id ' . $id, Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $category->getId(),
            'categoryName' => $category->getName(),
            'slug' => $category->getSlug(),
            'status' => $category->getStatus()
        ];

        return $this->json($data, Response::HTTP_OK);

    }


    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/category/{id}', name: 'category_update', methods: ['PUT', 'PATCH'])]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            return $this->json('Category data not found for id ' . $id, Response::HTTP_NOT_FOUND);
        }

        $category->setName($request->request->get('name'));
        $category->setSlug($request->request->get('slug'));
        $category->setStatus($request->request->get('status'));
        $entityManager->flush();

        $data = [
            'id' => $category->getId(),
            'categoryName' => $category->getName(),
            'slug' => $category->getSlug(),
            'status' => $category->getStatus()
        ];

        return $this->json($data, Response::HTTP_OK);
    }



    #[Route('/category/{id}', name: 'category_destroy', methods: ['DELETE'])]
    public function destroy(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            return $this->json('Category data not found for id ' . $id, Response::HTTP_OK);
        }
        $entityManager->remove($category);
        $entityManager->flush();

        $data = [
            'id' => $category->getId(),
            'categoryName' => $category->getName(),
            'slug' => $category->getSlug(),
            'status' => 'deleted'
        ];

        return $this->json($data, Response::HTTP_OK);

    }
}
