<?php

namespace Jakmall\Recruitment\Calculator\Http\Controller;

use Symfony\Component\HttpFoundation\Response;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;

class HistoryController
{

    protected $history;

    public function __construct(CommandHistoryManagerInterface $history)
    {
        $this->history = $history;
    }

    public function index()
    {
        $data = $this->history->findAll();

        $response = new Response();
        $response->setContent($this->convertToJSON($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    protected function convertToArray($item)
    {
        $input = preg_split("/(\d+)/", $item['description'], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $input = array_values(array_filter($input, 'is_numeric'));
        $result = [
            "id" => $item['id'],
            "command" => $item['command'],
            "operation" => $item['description'],
            "input" => $input,
            "result" => $item['result'],
            "time" => $item['time'],
        ];

        return $result;
    }

    protected function convertToJSON($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = $this->convertToArray($item);
        }

        return json_encode($result, JSON_NUMERIC_CHECK);
    }

    public function show($id)
    {
        $response = new Response();

        $data = $this->history->findById($id);

        $response->setContent(json_encode($this->convertToArray($data), JSON_NUMERIC_CHECK));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function remove($id)
    {
        $response = new Response();

        $result = $this->history->deleteById($id);

        if ($result) {
            $response->setStatusCode(204);
        } else {
            $response->setStatusCode(500);
        }

        return $response;
    }
}
