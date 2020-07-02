<?php

namespace Jakmall\Recruitment\Calculator\Http\Controller;

use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Jakmall\Recruitment\Calculator\Services\Calculator\Operation;
use Jakmall\Recruitment\Calculator\Services\Calculator\Operators\Sum;
use Jakmall\Recruitment\Calculator\Services\Calculator\Operators\Subtract;
use Jakmall\Recruitment\Calculator\Services\Calculator\Operators\Multiply;
use Jakmall\Recruitment\Calculator\Services\Calculator\Operators\Divide;
use Jakmall\Recruitment\Calculator\Services\Calculator\Operators\Exponent;

class CalculatorController
{
    protected $action;
    protected $operator;

    public function __construct(Operation $operation, CommandHistoryManagerInterface $history)
    {
        $this->operation = $operation;
        $this->history = $history;
    }

    public function calculate(Request $request, $action)
    {
        $numbers = $request->request->get('input');
        $response = new Response();
        $this->setAction($action);

        $getOperation = $this->getOperation($numbers);
        if (is_null($getOperation)) {
            $response->setStatusCode(400);
            $data = [
                "message" => "Wrong action"
            ];
        } else {
            $description = $this->operation->generateCalculationDescription($getOperation);
            $result = $this->operation->calculateAll($getOperation);

            $dataToSave = $this->getDataToSave($description, $result);
            $this->history->log($dataToSave);
            $data = [
                "command" => $dataToSave['command'],
                "operation" => $dataToSave['description'],
                "result" => $dataToSave['result']
            ];
        }

        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    protected function setAction($action): void
    {
        $this->action = $action;
    }

    protected function getAction(): string
    {
        $action = $this->action;
        return $action;
    }

    protected function getOperation($numbers)
    {
        switch ($this->getAction()) {
            case 'add':
                return new Sum($numbers);
                break;
            case 'subtract':
                return new Subtract($numbers);
                break;
            case 'multiply':
                return new Multiply($numbers);
                break;
            case 'divide':
                return new Divide($numbers);
                break;
            case 'pow':
                return new Exponent($numbers[0], $numbers[1]);
                break;
            default:
                return null;
                break;
        }
    }

    protected function getDataToSave($description, $result): array
    {
        $output = sprintf('%s = %s', $description, $result);
        $data = [
            "command" => $this->getAction(),
            "description" => $description,
            "result" => $result,
            "output" => $output,
        ];
        return $data;
    }
}
