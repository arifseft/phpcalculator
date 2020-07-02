<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;
use Jakmall\Recruitment\Calculator\Services\Calculator\Operation;
use Jakmall\Recruitment\Calculator\Services\Calculator\Operators\Exponent;

class PowCommand extends Command
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
            '%s {base : The base number} {exp : The exponent number}',
            $commandVerb
        );
        $this->description = "Exponent the given number";

        $this->operation = $operation;

        parent::__construct();
    }

    protected function getCommandVerb(): string
    {
        return 'pow';
    }

    public function handle(): void
    {
        $base = $this->argument("base");
        $exp = $this->argument("exp");
        $pow = new Exponent($base, $exp);
        $description = $this->operation->generateCalculationDescription($pow);
        $result = $this->operation->calculateAll($pow);

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
