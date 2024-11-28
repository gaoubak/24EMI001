<?php

namespace App\Controller;

use App\DTO\ScheduleDTO;
use App\Entity\Schedule;
use App\Repository\ScheduleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends BaseController
{
    #[Route('/api/schedule', name: 'get_schedules', methods: ['GET'])]
    public function index(ScheduleRepository $scheduleRepository): JsonResponse
    {
        $schedules = $scheduleRepository->findAll();

        return $this->getSerializedResponse($schedules, "get_schedule");
    }

    #[Route('/api/schedule', name: 'create_schedule', methods: ['POST'])]
    public function create(ScheduleDTO $scheduleDTO): JsonResponse
    {
        $schedule = $this->createFromDTO(Schedule::class, $scheduleDTO);

        $this->em->persist($schedule);
        $this->em->flush();

        return $this->getSerializedResponse($schedule, "get_schedule");
    }

    #[Route('/api/schedule/{id}', name: 'update_schedule', methods: ['PUT'])]
    public function update(Schedule $schedule, ScheduleDTO $scheduleDTO): JsonResponse
    {
        $this->updateEntityFromDTO($schedule, $scheduleDTO);

        $this->em->flush();

        return $this->getSerializedResponse($schedule, "get_schedule");
    }

    #[Route('/api/schedule/{id}', name: 'delete_schedule', methods: ['DELETE'])]
    public function delete(Schedule $schedule): JsonResponse
    {
        $this->em->remove($schedule);
        $this->em->flush();

        return new JsonResponse(['status' => 'Schedule deleted successfully']);
    }
}
