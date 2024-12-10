<?php declare(strict_types = 1);

namespace Picamator\TransferObject\Command;

use Picamator\TransferObject\Generated\ConfigTransfer;
use Picamator\TransferObject\Generated\DefinitionPathTransfer;
use Picamator\TransferObject\Generated\GeneratedPathTransfer;
use Picamator\TransferObject\Generator\GeneratorFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratorCommand extends Command
{
    use WritelnTrait;
    use ProgressBarTrait;

    private const string ROOT_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR;

    private const string NAME = 'generate:transfer';
    private const string DESCRIPTION = 'Generates Transfer Objects based on definitions.';
    private const string HELP = 'In order to generate Transfer Objects path to configuration should be provided. Please check "config/generator/config.yml.dist".';

    protected function configure(): void
    {
        $this->setName(static::NAME)
            ->setDescription(static::DESCRIPTION)
            ->setHelp(static::HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configTransfer = new ConfigTransfer()
            ->fromArray([
                ConfigTransfer::CLASS_NAMESPACE => 'Picamator\TransferObject\Generated',
                ConfigTransfer::DEFINITION_PATH => [
                    DefinitionPathTransfer::PATH => static::ROOT_PATH . 'config/definition',
                ],
                ConfigTransfer::GENERATED_PATH => [
                    GeneratedPathTransfer::PATH => static::ROOT_PATH . 'Generated',
                ],
            ]);

        $generatorFiber = new GeneratorFactory($configTransfer)->createGeneratorFiber()->getGeneratorFiber();

        /** @var int $definitionFileCount */
        $definitionFileCount = $generatorFiber->start();
        $this->progressStart($input, $output, $definitionFileCount);

        while (!$generatorFiber->isTerminated()) {
            /** @var \Picamator\TransferObject\Generated\GeneratorTransfer|null $generatorTransfer */
            $generatorTransfer = $generatorFiber->resume();
            if ($generatorTransfer !== null) {
                $this->progressAdvance($generatorTransfer->definitionKey);
                $this->writelnGeneratorTransfer($generatorTransfer, $output);
            }
        }

        $this->progressFinish();

        return $generatorFiber->getReturn() ? Command::SUCCESS : Command::FAILURE;
    }
}
