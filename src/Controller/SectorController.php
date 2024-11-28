<?php

namespace App\Controller;

use App\DTO\SectorDTO;
use App\Entity\Sector;
use App\Repository\SectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SectorController extends BaseController
{
    #[Route('/api/sector', name: 'get_sectors', methods: ['GET'])]
    public function index(SectorRepository $sectorRepository): JsonResponse
    {
        $sectors = $sectorRepository->findAll();

        return $this->getSerializedResponse($sectors, "get_sector");
    }

    #[Route('/api/sector/{id}', name: 'get_sector', methods: ['GET'])]
    public function show(Sector $sector): JsonResponse
    {
        return $this->getSerializedResponse($sector, "get_sector");
    }

    #[Route('/api/sector', name: 'create_sector', methods: ['POST'])]
    public function create(SectorDTO $sectorDTO): JsonResponse
    {
        $sector = $this->createFromDTO(Sector::class, $sectorDTO);

        $this->em->persist($sector);
        $this->em->flush();

        return $this->getSerializedResponse($sector, "get_sector");
    }

    #[Route('/api/sector/{id}', name: 'update_sector', methods: ['PUT'])]
    public function update(Sector $sector, SectorDTO $sectorDTO): JsonResponse
    {
        $this->updateEntityFromDTO($sector, $sectorDTO);

        $this->em->flush();

        return $this->getSerializedResponse($sector, "get_sector");
    }

    #[Route('/api/sector/{id}', name: 'delete_sector', methods: ['DELETE'])]
    public function delete(Sector $sector): JsonResponse
    {
        $this->em->remove($sector);
        $this->em->flush();

        return new JsonResponse(['status' => 'Sector deleted successfully']);
    }
}
