<?php

namespace Jakmall\Recruitment\Calculator\Services\Calculator\Operators;

use Jakmall\Recruitment\Calculator\Services\Calculator\AbstractOperator;

class Subtract extends AbstractOperator
{
    protected $numbers;

    public function __construct($numbers)
    {
        $this->numbers = $numbers;
    }

    public function calculateAll()
    {
        $number = array_pop($this->numbers);

        if (count($this->numbers) <= 0) {
            return $number;
        }

        return $this->calculate($this->calculateAll($this->numbers), $number);
    }

    /**
     * @param int|float $number1
     * @param int|float $number2
     *
     * @return int|float
     */
    protected function calculate($number1, $number2)
    {
        return $number1 - $number2;
    }

    public function generateCalculationDescription(): string
    {
        $operator = $this->getOperator();
        $glue = sprintf(' %s ', $operator);

        return implode($glue, $this->numbers);
    }

    protected function getOperator(): string
    {
        return '-';
    }
}
