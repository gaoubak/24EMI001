<?php

namespace App\Service;

use App\Attribute\DTORelationAttribute;
use App\Attribute\DTODecimalAttribute;
use App\DTO\Input\DTOInputInterface;
use Doctrine\ORM\EntityManagerInterface;

class DTOTransformerService
{
    public function __construct(
        private EntityManagerInterface $em
    ) 
    {
    }

    private function populateEntityFromDTO($entity, DTOInputInterface $DTO): void
    {
        $reflectionClass = new \ReflectionClass($DTO);
        foreach ($reflectionClass->getProperties() as $property) {
            $key = $property->getName();
            $value = $property->getValue($DTO);

            if (property_exists($entity, $key)) {
                if ($value !== null && $DTORelationAttributes = $property->getAttributes(DTORelationAttribute::class)) {
                    $DTORelationAttribute = $DTORelationAttributes[0];

                    $arguments = $DTORelationAttribute->getArguments();
                    $relatedEntity = $this->em->getRepository($arguments["class"])->findOneById($value);

                    if (!$relatedEntity) {
                        throw new \Exception("Related entity not found");
                    }

                    $value = $relatedEntity;
                }

                if ($value !== null && $DTODecimalAttribute = $property->getAttributes(DTODecimalAttribute::class)) {
                    $value = str_replace(",", ".", $value);
                }

                $entity->{'set' . ucfirst($key)}($value);
            }
        }
    }

    public function createFromDTO(string $class, DTOInputInterface $DTO): mixed
    {
        $createdEntity = new $class();
        $this->populateEntityFromDTO($createdEntity, $DTO);

        return $createdEntity;
    }

    public function updateEntityFromDTO($entity, DTOInputInterface $DTO): void
    {
        $this->populateEntityFromDTO($entity, $DTO);
    }
}