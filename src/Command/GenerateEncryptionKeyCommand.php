<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use ParagonIE\Halite\KeyFactory;


#[AsCommand(
    name: 'app:generate-encryption-key',
    description: 'Generate database encryption key.'
)]
class GenerateEncryptionKeyCommand extends Command
{
    public function __construct(
        private string $encryptionKeyPath
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!file_exists($this->encryptionKeyPath)) {
            $encryptionKey = KeyFactory::generateEncryptionKey();
            KeyFactory::save($encryptionKey, $this->encryptionKeyPath);
            $output->writeln('Encryption key has been created');
            return Command::SUCCESS;
        } else {
            $output->writeln('Encryption key already exists');
            return Command::FAILURE;
        }
    }
}