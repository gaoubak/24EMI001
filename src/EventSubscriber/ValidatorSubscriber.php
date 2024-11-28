<?php

namespace App\EventSubscriber;

use App\Service\ValidatorService;
use Doctrine\Common\EventSubscriber;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping as ORM;

#[AsDoctrineListener(event: Events::prePersist, connection: 'default')]
#[AsDoctrineListener(event: Events::preUpdate, connection: 'default')]
class ValidatorSubscriber implements EventSubscriber
{
    public function __construct(
        private ValidatorService $validatorService
    )
    {

    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->validatorService->validate($args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->validatorService->validate($args->getObject());
    }
}