<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
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

    public function __construct(Operation $operation)
    {
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

        $this->comment(sprintf('%s = %s', $description, $result));
    }
}
