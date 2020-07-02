<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;

class HistoryListCommand extends Command
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var string
     */
    protected $description;

    protected $history;

    public function __construct(CommandHistoryManagerInterface $history)
    {
        $this->history = $history;
        $this->signature = $this->getSignature();
        $this->description = "Show Calculator History";
        parent::__construct();
    }

    protected function getCommandVerb(): string
    {
        return 'history:list';
    }

    protected function getSignature(): string
    {
        return 'history:list {commands?* : Filter the history by commands} {--D|driver=database : Driver for storage connection}';
    }

    public function handle(): void
    {
        $data = $this->getData($this->argument('commands'), $this->option());

        $this->displayOutput($data);
    }

    protected function getHeaders(): array
    {
        return ['no', 'command', 'description', 'result', 'output', 'time'];
    }

    protected function getOption(): string
    {
        return $this->option('driver');
    }

    protected function getData($arguments, $option): array
    {
        /*$allowedOption = ['file', 'database'];
        if (in_array($option, $allowedOption)) {
            $filepath = "Storage/{$option}/records";
        } else {
            die("Driver option only [file|database]\n");
        }
        $fileexist = file_exists($filepath);
        if ($fileexist) {
            $storage = unserialize(file_get_contents($filepath));
            if (count($arguments) > 0) {
                $filteredData = [];
                foreach ($arguments as $key => $value) {
                    $data = array_filter($storage, function($item) use ($value) {
                        return $item['command'] == $value;
                    });
                    $filteredData = array_merge($filteredData, $data);
                }
                usort($filteredData, function($a, $b) {
                    return $a['no'] - $b['no'];
                });
                $storage = $filteredData;
            }
        } else {
            $storage = [];
        }
        return $storage;*/
        if (count($arguments) > 0) {
            $data = $this->history->findByCommand($arguments);
        } else {
            $data = $this->history->findAll();
        }

        return $data;
    }

    protected function displayOutput($data): void
    {
        $headers = $this->getHeaders();
        $result = [];
        foreach ($data as $dataKey => $dataValue) {
            $item = [];
            foreach ($headers as $headerValue) {
                $item[$headerValue] = $dataValue[$headerValue];
            }
            $item['no'] = $dataKey + 1;
            $result[] = $item;
        }
        if (count($result) > 0) {
            $this->table(array_map('ucfirst', $headers), $result);
        } else {
            $this->info('History is Empty');
        }
    }
}
