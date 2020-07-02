<?php

namespace Jakmall\Recruitment\Calculator\Services\Calculator;

abstract class AbstractOperator implements OperationInterface
{
    abstract public function calculateAll();
    abstract public function generateCalculationDescription(): string;
}
