<?php

namespace Jakmall\Recruitment\Calculator\Services\Calculator\Operators;

use Jakmall\Recruitment\Calculator\Services\Calculator\AbstractOperator;

class Exponent extends AbstractOperator
{
    protected $base;
    protected $exp;

    public function __construct($base, $exp)
    {
        $this->base = $base;
        $this->exp = $exp;
    }

    public function calculateAll()
    {
        return pow($this->base, $this->exp);
    }

    public function generateCalculationDescription(): string
    {
        $operator = $this->getOperator();
        $description = sprintf('%d %s %d', $this->base, $operator, $this->exp);

        return $description;
    }

    protected function getOperator(): string
    {
        return '^';
    }
}
