<?php

namespace Jakmall\Recruitment\Calculator\Services\Calculator;

use Jakmall\Recruitment\Calculator\Services\Calculator\OperationInterface;

class Operation
{
    public function calculateAll(OperationInterface $operation)
    {
        return $operation->calculateAll();
    }

    public function generateCalculationDescription(OperationInterface $operation): string
    {
        return $operation->generateCalculationDescription();
    }
}
