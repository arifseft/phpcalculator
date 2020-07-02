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

    protected function convertToJSON($data)
    {
        $result = [];
        foreach ($data as $item) {
            $input = preg_split("/(\d+)/", $item['description'], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $input = array_values(array_filter($input, 'is_numeric'));
            $result[] = [
                "id" => $item['id'],
                "command" => $item['command'],
                "operation" => $item['description'],
                "input" => $input,
                "result" => $item['result'],
                "time" => $item['time'],
            ];
        }

        return json_encode($result, JSON_NUMERIC_CHECK);
    }

    public function show()
    {
        dd('create show history by id here');
    }

    public function remove()
    {
        // todo: modify codes to remove history
        dd('create remove history logic here');
    }
}
