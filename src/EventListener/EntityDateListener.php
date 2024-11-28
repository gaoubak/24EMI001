<?php

namespace App\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use App\Entity\ConstructionSiteVersion;
use App\Entity\ClientInvoice;
use App\Entity\KeyData;
use App\Entity\WorkSheet;
use App\Entity\ClientContrat;
use App\Entity\WorkSheetLot;
use App\Entity\Lot;
use App\Entity\Provision;
use App\Entity\SupplierContract;
use App\Entity\SupplierInvoice;
use App\Entity\Supplier;

class EntityDateListener
{
	public function onFlush(OnFlushEventArgs $eventArgs)
    {
    	$em = $eventArgs->getObjectManager();
        $uow = $em->getUnitOfWork();

        $updates = $uow->getScheduledEntityUpdates();
        $insertions = $uow->getScheduledEntityInsertions();

        foreach ([...$updates, ...$insertions] as $entity) {
        	$version = $this->getVersion($entity);
        	$this->updateVersion($version, $em, $uow);
        }
    }

    private function getVersion($entity): ?ConstructionSiteVersion {

    	$version = match(true) {
        	$entity instanceof ClientInvoice => $entity->getconstructionSiteVersion(),
        	$entity instanceof KeyData => $entity->getVersion(),
        	$entity instanceof WorkSheet => $entity->getVersion(),
        	$entity instanceof ClientContrat => $entity->getWorkSheet()?->getVersion(),
        	$entity instanceof WorkSheetLot => $entity->getWorkSheet()?->getVersion(),
        	$entity instanceof Lot => $entity->getVersion(),
        	$entity instanceof Provision => $entity->getLot()?->getVersion(),
        	$entity instanceof SupplierContract => $entity->getLot()?->getVersion(),
        	$entity instanceof SupplierInvoice => $entity->getLot()?->getVersion(),
        	$entity instanceof Supplier => $entity->getLot()?->getVersion(),
            $entity instanceof Transfer => $entity->getOriginLot()?->getVersion(),
        	default => null
        };
        return $version;
    }

    private function updateVersion(?ConstructionSiteVersion $version, $em, $uow): void {

    	if(!empty($version)) {
        	$version->setModificationDate(new \DateTime());
        	$em->persist($version);
        	$uow->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($version)), $version);
        }
    }
}
