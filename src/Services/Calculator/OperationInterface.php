<?php

namespace Jakmall\Recruitment\Calculator\Services\Calculator;

interface OperationInterface
{
    public function calculateAll();
    public function generateCalculationDescription(): string;
}
