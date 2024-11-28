<?php

namespace App\Command;

use App\Service\CsvReader;
use App\Service\VehicleSynchronizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;


#[AsCommand(
    name: 'app:sync-vehicleCatalogue-csv',
    description: 'Sync Vehicle from csv to db.'
)]
class SyncVehiclesCommand extends Command
{
    private $csvReader;
    private $vehicleSynchronizer;

    public function __construct(CsvReader $csvReader, VehicleSynchronizer $vehicleSynchronizer)
    {
        parent::__construct();
        $this->csvReader = $csvReader;
        $this->vehicleSynchronizer = $vehicleSynchronizer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = './public/dataVehicle.csv';
        $data = $this->csvReader->readCsv($filePath);
        $this->vehicleSynchronizer->sync($data);

        $output->writeln('Vehicle data synchronized successfully!');
        return Command::SUCCESS;
    }
}

