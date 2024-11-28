<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Stripe\StripeClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\StripePrice;

#[AsCommand(
    name: 'app:sync-stripe-products',
    description: 'Synchronize products from Stripe and create or update StripePrice entities, including price data.',
)]
class SyncStripeProductsCommand extends Command
{
    private StripeClient $stripe; // Type hint for better code readability
    private EntityManagerInterface $entityManager;

    public function __construct(string $stripeSecretKey, EntityManagerInterface $entityManager)
    {
        $this->stripe = new StripeClient($stripeSecretKey);
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        // No need for arguments or options in this case
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting to synchronize products and prices from Stripe...');
    
        try {
            // Fetch all products from Stripe
            $stripeProducts = $this->stripe->products->all();

            foreach ($stripeProducts->data as $product) {
                $output->writeln('Processing product: ' . $product->name);
                
                // Fetch all prices associated with this product
                $prices = $this->stripe->prices->all(['product' => $product->id]);

                foreach ($prices->data as $price) {
                    $vehicleType = $price->metadata->vehicleType ?? null;
                    $subType = $price->metadata->subType ?? null;
                    $coverageType = $price->metadata->coverageType ?? null;
                    $description = $price->metadata->description?? null;

                    // Check if the StripePrice entity already exists
                    $stripePriceEntity = $this->entityManager->getRepository(StripePrice::class)->findOneBy([
                        'stripePriceId' => $price->id
                    ]);

                    if (!$stripePriceEntity) {
                        // Create new StripePrice entity
                        $stripePriceEntity = new StripePrice();
                        $output->writeln('Created new price for product: ' . $product->name);
                    } else {
                        // Update existing StripePrice entity
                        $output->writeln('Updating existing price for product: ' . $product->name);
                    }

                    $unitAmount = (float)$price->unit_amount; 

                    // Set values regardless of whether it's new or existing
                    $stripePriceEntity->setStripePriceId($price->id);
                    $formattedPrice = number_format($unitAmount / 100.0, 2, '.', '');
                    $stripePriceEntity->setUnitAmount($formattedPrice);
                    $stripePriceEntity->setBillingInterval($price->recurring ? $price->recurring->interval : null);
                    $stripePriceEntity->setIntervalCount($price->recurring ? $price->recurring->interval_count : null);
                    
                    // Set metadata fields
                    $stripePriceEntity->setVehicleType($vehicleType);
                    $stripePriceEntity->setSubType($subType);
                    $stripePriceEntity->setCoverageType($coverageType);
                    $stripePriceEntity->setDescription($description);
                     
                    // Set the associated product ID
                    $stripePriceEntity->setStripeProductId($product->id); 
                    
                    // Persist the price entity
                    $this->entityManager->persist($stripePriceEntity);
                }
            }
    
            // Flush all changes to the database
            $this->entityManager->flush();
            $output->writeln('Synchronization complete.');
    
            return Command::SUCCESS;
    
        } catch (\Exception $e) {
            $output->writeln('Error synchronizing products: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }    
}
