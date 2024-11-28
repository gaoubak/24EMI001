<?php

namespace App\Controller;

use App\DTO\Input\DTOInputInterface;
use App\Service\DTOTransformerService;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;


class BaseController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected EntityManagerInterface $em,
        protected ValidatorService $validatorService,
        protected DTOTransformerService $dtoTransformerService
    )
    {

    }

    protected function getSerializedResponse($data, $group) {
        $context = [
            'groups' => $group
        ];

        return new JsonResponse(json_decode($this->serializer->serialize($data, 'json', $context)), 200);
    }

    protected function createFromDTO(string $class, DTOInputInterface $DTO): mixed
    {
        return $this->dtoTransformerService->createFromDTO($class, $DTO);
    }

    protected function updateEntityFromDTO($entity, DTOInputInterface $DTO): void
    {
        $this->dtoTransformerService->updateEntityFromDTO($entity, $DTO);
    }
}
