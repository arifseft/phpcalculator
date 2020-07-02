<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;
use Jakmall\Recruitment\Calculator\Services\Calculator\Operation;
use Jakmall\Recruitment\Calculator\Services\Calculator\Operators\Multiply;

class MultiplyCommand extends Command
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var string
     */
    protected $description;

    protected $operation;

    protected $history;

    public function __construct(Operation $operation, CommandHistoryManagerInterface $history)
    {

        $this->history = $history;

        $commandVerb = $this->getCommandVerb();

        $this->signature = sprintf(
            '%s {numbers* : The numbers to be %s}',
            $commandVerb,
            $this->getCommandPassiveVerb()
        );
        $this->description = sprintf('%s all given Numbers', ucfirst($commandVerb));

        $this->operation = $operation;

        parent::__construct();
    }

    protected function getCommandVerb(): string
    {
        return 'multiply';
    }

    protected function getCommandPassiveVerb(): string
    {
        return 'multiplied';
    }

    public function handle(): void
    {
        $numbers = $this->argument("numbers");
        $multiply = new Multiply($numbers);
        $description = $this->operation->generateCalculationDescription($multiply);
        $result = $this->operation->calculateAll($multiply);

        $dataToSave = $this->getDataToSave($description, $result);
        $this->history->log($dataToSave);

        $this->comment(sprintf('%s = %s', $description, $result));
    }

    protected function getDataToSave($description, $result): array
    {
        $output = sprintf('%s = %s', $description, $result);
        $data = [
            "command" => ucfirst($this->getCommandVerb()),
            "description" => $description,
            "result" => $result,
            "output" => $output,
        ];
        return $data;
    }
}
