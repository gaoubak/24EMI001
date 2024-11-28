<?php

namespace App\EventSubscriber;

use App\Attribute\Encrypted;
use App\Attribute\BlindIndexed;
use App\Attribute\Encyrpted;
use App\Service\BlindIndexService;
use Doctrine\Common\EventSubscriber;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto as Symmetric;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\HiddenString\HiddenString;

#[AsDoctrineListener(event: Events::prePersist, connection: 'default')]
#[AsDoctrineListener(event: Events::preUpdate, connection: 'default')]
#[AsDoctrineListener(event: Events::postLoad, connection: 'default')]
#[AsDoctrineListener(event: Events::postFlush, connection: 'default')]
class EncryptionSubscriber implements EventSubscriber
{
    private EncryptionKey $encryptionKey;
    private array $postFlushEntities = []; // Explicitly type the array

    public function __construct(
        string $encryptionKeyPath,
        private BlindIndexService $blindIndexService
    )
    {
        $this->encryptionKey = KeyFactory::loadEncryptionKey($encryptionKeyPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array // Add the return type declaration
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postFlush,
            Events::postLoad,
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->encryptEntity($args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->encryptEntity($args->getObject());
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $this->decryptEntity($args->getObjectManager(), $args->getObject());
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        foreach ($this->postFlushEntities as $entity) {
            $this->decryptEntity($args->getObjectManager(), $entity);
        }
        
        $this->postFlushEntities = [];
    }

    private function encryptEntity($entity): void
    {
        $hasEncryptedField = false;

        $reflectionClass = new \ReflectionClass($entity);
        foreach ($reflectionClass->getProperties() as $property) {
            if ($blindIndexProperties = $property->getAttributes(BlindIndexed::class)) {
                $blindIndexProperty = $blindIndexProperties[0];
                $propertyName = $blindIndexProperty->getArguments()["targetPropertyName"];
                $decryptedValue = $property->getValue($entity);
                $blindIndex = $this->blindIndexService->getBlindIndex($decryptedValue);
                $entity->{'set' . ucfirst($propertyName)}($blindIndex);
            }

            if (count($property->getAttributes(Encyrpted::class)) > 0) {
                $hasEncryptedField = true;
                $decryptedValue = $property->getValue($entity);
                $encryptedValue = Symmetric::encrypt(new HiddenString($decryptedValue), $this->encryptionKey);
                $property->setValue($entity, $encryptedValue);
            }
        }

        if ($hasEncryptedField) {
            $this->postFlushEntities[] = $entity;
        }
    }

    private function decryptEntity($em, $entity): void
    {
        $reflectionClass = new \ReflectionClass($em->getClassMetadata(get_class($entity))->getName());
        foreach ($reflectionClass->getProperties() as $property) {
            if (count($property->getAttributes(Encyrpted::class)) > 0) {
                $encryptedValue = $property->getValue($entity);
                $decryptedValue = Symmetric::decrypt($encryptedValue, $this->encryptionKey);
                $property->setValue($entity, $decryptedValue->getString());
            }
        }
    }
}
