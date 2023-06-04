<?php

namespace App\Controller;

use App\Entity\Brand;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'brand_api_')]
class BrandController extends AbstractController
{

    /**
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/brand', name: 'brand_list', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $brands = $doctrine->getRepository(Brand::class)->findAll();
        $data = [];
        foreach ($brands as $brand) {
            $data[] = [
                'id' => $brand->getId(),
                'name' => $brand->getName(),
                'slug' => $brand->getSlug(),
                'status' => $brand->getStatus()
            ];
        }
        return $this->json($data);
    }


    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/brand', name: 'brand_create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $brand = new Brand();
        $brand->setName($request->request->get('name'));
        $brand->setSlug($request->request->get('slug'));
        $brand->setStatus($request->request->get('status'));
        $entityManager->persist($brand);
        $entityManager->flush();

        $data = [
            'id' => $brand->getId(),
            'name' => $brand->getName(),
            'slug' => $brand->getSlug(),
            'status' => $brand->getStatus()
        ];
        return $this->json($data, Response::HTTP_CREATED);

    }

    /**
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/brand/{id}', name: 'brand_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $brand = $doctrine->getRepository(Brand::class)->find($id);
        if (!$brand) {
            return $this->json('Data not found for id ' . $id, Response::HTTP_NOT_FOUND);
        }
        $data = [
            'id' => $brand->getId(),
            'name' => $brand->getName(),
            'slug' => $brand->getSlug(),
            'status' => $brand->getStatus()
        ];

        return $this->json($data, Response::HTTP_OK);
    }


    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/brand/{id}', name: 'brand_update', methods: ['PUT', 'PATCH'])]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $brand = $entityManager->getRepository(Brand::class)->find($id);
        if (!$brand) {
            return $this->json('Data not found for id ' . $id, Response::HTTP_NOT_FOUND);
        }

        $brand->setName($request->request->get('name'));
        $brand->setSlug($request->request->get('slug'));
        $brand->setStatus($request->request->get('status'));
        $entityManager->flush();

        $data = [
            'id' => $brand->getId(),
            'name' => $brand->getName(),
            'slug' => $brand->getSlug(),
            'status' => $brand->getStatus()
        ];

        return $this->json($data, Response::HTTP_OK);

    }

    /**
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/brand/{id}', name: 'brand_destroy', methods: ['DELETE'])]
    public function destroy(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $brand = $entityManager->getRepository(Brand::class)->find($id);
        if (!$brand) {
            return $this->json('Data not found for id ' . $id, Response::HTTP_OK);
        }
        $entityManager->remove($brand);
        $entityManager->flush();
        return $this->json('Successfully deleted brand for id ' . $id, Response::HTTP_OK);
    }


}
