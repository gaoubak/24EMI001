<?php

namespace App\Controller;

use App\DTO\InterventionDTO;
use App\Entity\Intervention;
use App\Repository\InterventionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class InterventionController extends BaseController
{
    #[Route('/api/intervention', name: 'get_interventions', methods: ['GET'])]
    public function index(InterventionRepository $repository): JsonResponse
    {
        $interventions = $repository->findAll();

        return $this->getSerializedResponse($interventions, "get_intervention");
    }

    #[Route('/api/intervention/{id}', name: 'get_intervention', methods: ['GET'])]
    public function show(Intervention $intervention): JsonResponse
    {
        return $this->getSerializedResponse($intervention, "get_intervention");
    }

    #[Route('/api/intervention/{id}/participants', name: 'get_intervention_participants', methods: ['GET'])]
    public function participants(Intervention $intervention): JsonResponse
    {
        $participants = $intervention->getParticipants();

        return $this->getSerializedResponse($participants, "get_intervention_participants");
    }

    #[Route('/api/intervention/{id}/participants/{participantId}', name: 'get_intervention_participant', methods: ['GET'])]
    public function showParticipant(Intervention $intervention, int $participantId): JsonResponse
    {
        $participant = $intervention->getParticipantById($participantId);

        if (!$participant) {
            throw $this->createNotFoundException('Participant not found');
        }

        return $this->getSerializedResponse($participant, "get_intervention_participant");
    }

    #[Route('/api/intervention/{id}/participants/{participantId}', name: 'add_intervention_participant', methods: ['POST'])]
    public function addParticipant(Intervention $intervention, int $participantId): JsonResponse
    {
        $participant = $this->em->getRepository(Participant::class)->find($participantId);

        if (!$participant) {
            throw $this->createNotFoundException('Participant not found');
        }

        $intervention->addParticipant($participant);
        $this->em->flush();

        return $this->getSerializedResponse($participant, "get_intervention_participant");
    }

    #[Route('/api/intervention', name: 'create_intervention', methods: ['POST'])]
    public function create(InterventionDTO $dto): JsonResponse
    {
        $intervention = $this->createFromDTO(Intervention::class, $dto);

        $this->em->persist($intervention);
        $this->em->flush();

        return $this->getSerializedResponse($intervention, "get_intervention");
    }

    #[Route('/api/intervention/{id}', name: 'update_intervention', methods: ['PUT'])]
    public function update(Intervention $intervention, InterventionDTO $dto): JsonResponse
    {
        $this->updateEntityFromDTO($intervention, $dto);

        $this->em->flush();

        return $this->getSerializedResponse($intervention, "get_intervention");
    }

    #[Route('/api/intervention/{id}', name: 'delete_intervention', methods: ['DELETE'])]
    public function delete(Intervention $intervention): JsonResponse
    {
        $this->em->remove($intervention);
        $this->em->flush();

        return new JsonResponse(['status' => 'Intervention deleted successfully']);
    }
}
