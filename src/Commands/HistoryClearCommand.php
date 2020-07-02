<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;

class HistoryClearCommand extends Command
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
        $commandVerb = $this->getCommandVerb();

        $this->signature = $this->getSignature();
        $this->description = "Clear saved history";
        parent::__construct();
    }

    protected function getCommandVerb(): string
    {
        return 'history:clear';
    }
    protected function getSignature(): string
    {
        $signature = "history:clear";
        return $signature;
    }

    public function handle(): void
    {
        $this->deleteHistoryData();
    }

    protected function deleteHistoryData(): void
    {
        $this->history->clearAll();
        $filePath = 'Storage/file/records';
        if (file_exists($filePath)) {
            unlink('Storage/file/records');
        }
        $this->info('History cleared!');
    }
}
