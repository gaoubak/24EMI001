<?php

namespace App\Controller;

use App\DTO\GeolocationDTO;
use App\Entity\Geolocation;
use App\Repository\GeolocationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GeolocationController extends BaseController
{
    #[Route('/api/geolocation', name: 'get_geolocations', methods: ['GET'])]
    public function index(GeolocationRepository $geolocationRepository): JsonResponse
    {
        $geolocations = $geolocationRepository->findAll();

        return $this->getSerializedResponse($geolocations, "get_geolocation");
    }

    #[Route('/api/geolocation/{id}', name: 'get_geolocation', methods: ['GET'])]
    public function show(Geolocation $geolocation): JsonResponse
    {
        return $this->getSerializedResponse($geolocation, "get_geolocation");
    }

    #[Route('/api/geolocation/{id}/interventions', name: 'get_geolocation_interventions', methods: ['GET'])]
    public function getInterventions(Geolocation $geolocation): JsonResponse
    {
        return $this->getSerializedResponse($geolocation->getInterventions(), "get_geolocation_interventions");
    }

    #[Route('/api/geolocation/{id}/interventions/{interventionId}', name: 'add_geolocation_intervention', methods: ['POST'])]

    #[Route('/api/geolocation', name: 'create_geolocation', methods: ['POST'])]
    public function create(GeolocationDTO $geolocationDTO): JsonResponse
    {
        $geolocation = $this->createFromDTO(Geolocation::class, $geolocationDTO);

        $this->em->persist($geolocation);
        $this->em->flush();

        return $this->getSerializedResponse($geolocation, "get_geolocation");
    }

    #[Route('/api/geolocation/{id}', name: 'update_geolocation', methods: ['PUT'])]
    public function update(Geolocation $geolocation, GeolocationDTO $geolocationDTO): JsonResponse
    {
        $this->updateEntityFromDTO($geolocation, $geolocationDTO);

        $this->em->flush();

        return $this->getSerializedResponse($geolocation, "get_geolocation");
    }

    #[Route('/api/geolocation/{id}', name: 'delete_geolocation', methods: ['DELETE'])]
    public function delete(Geolocation $geolocation): JsonResponse
    {
        $this->em->remove($geolocation);
        $this->em->flush();

        return new JsonResponse(['status' => 'Geolocation deleted successfully']);
    }
}
