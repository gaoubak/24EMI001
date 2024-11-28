<?php

namespace App\Controller;

use App\DTO\AgencyDTO;
use App\Entity\Agency;
use App\Repository\AgencyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AgencyController extends BaseController
{
    #[Route('/api/Agency', name: 'get_Agencys', methods: ['GET'])]
    public function index(AgencyRepository $repository): JsonResponse
    {
        $Agencys = $repository->findAll();

        return $this->getSerializedResponse($Agencys, "get_Agency");
    }

    #[Route('/api/Agency/{id}', name: 'get_Agency', methods: ['GET'])]
    public function show(Agency $Agency): JsonResponse
    {
        return $this->getSerializedResponse($Agency, "get_Agency");
    }

    #[Route('/api/Agency', name: 'create_Agency', methods: ['POST'])]
    public function create(AgencyDTO $dto): JsonResponse
    {
        $Agency = $this->createFromDTO(Agency::class, $dto);

        $this->em->persist($Agency);
        $this->em->flush();

        return $this->getSerializedResponse($Agency, "get_Agency");
    }

    #[Route('/api/Agency/{id}', name: 'update_Agency', methods: ['PUT'])]
    public function update(Agency $Agency, AgencyDTO $dto): JsonResponse
    {
        $this->updateEntityFromDTO($Agency, $dto);

        $this->em->flush();

        return $this->getSerializedResponse($Agency, "get_Agency");
    }

    #[Route('/api/Agency/{id}', name: 'delete_Agency', methods: ['DELETE'])]
    public function delete(Agency $Agency): JsonResponse
    {
        $this->em->remove($Agency);
        $this->em->flush();

        return new JsonResponse(['status' => 'Agency deleted successfully']);
    }
}
