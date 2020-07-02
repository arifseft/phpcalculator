<?php
namespace Jakmall\Recruitment\Calculator\History;

use Exception;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;
use Jakmall\Recruitment\Calculator\Model\History;

class CommandHistory implements CommandHistoryManagerInterface
{
    protected $entityManager;

    public function __construct()
    {
        $this->entityManager = require __DIR__.'/../../bootstrap.php';
    }

    /**
     * Returns array of command history.
     *
     * @return array
     */
    public function findAll(): array
    {
        $historyRepository = $this->entityManager->getRepository(History::class);
        $histories = $historyRepository->findAll();
        $data = $this->convertToArray($histories);
        return $data;
    }

    /**
     * Log command data to storage.
     *
     * @param mixed $command The command to log.
     *
     * @return bool Returns true when command is logged successfully, false otherwise.
     */
    public function log($command): bool
    {
        try {
            $history = new History();
            $history->setCommand($command['command']);
            $history->setDescription($command['description']);
            $history->setOutput($command['output']);
            $history->setResult($command['result']);
            $history->setTime(new \DateTime("now", new \DateTimeZone('Asia/Jakarta')));
            $this->entityManager->persist($history);
            $this->entityManager->flush();
            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Clear all data from storage.
     *
     * @return bool Returns true if all data is cleared successfully, false otherwise.
     */
    public function clearAll():bool
    {
        try {
            $qb = $this->entityManager->createQueryBuilder();
            $qb->delete(History::class)->getQuery()->getResult();
            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findByCommand(array $arguments): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $history = $qb->select('h')->from(History::class, 'h');
        foreach ($arguments as $key => $value) {
            error_log($value);
            $history = $history->orWhere("h.command = ?" . (string)$key)->setParameter($key, ucfirst($value));
        }
        $query = $history->getQuery();
        $result = $query->getResult();
        $result = $this->convertToArray($result);

        return $result;
    }

    protected function convertToArray($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                "id" => $item->getId(),
                "command" => $item->getCommand(),
                "description" => $item->getDescription(),
                "output" => $item->getOutput(),
                "result" => $item->getResult(),
                "time" => $item->getTime(),
            ];
        }

        return $result;
    }
}
