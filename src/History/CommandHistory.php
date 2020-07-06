<?php
namespace Jakmall\Recruitment\Calculator\History;

use Exception;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;
use Jakmall\Recruitment\Calculator\Model\History;
use Nahid\JsonQ\Jsonq;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

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
    public function findAll($driver): array
    {
        $data = [];
        if ($driver == 'file') {
            $histories = file_get_contents("./Storage/db.json");
            $histories = json_decode($histories, true);

            if (!is_null($histories)) {
                $data = $histories;
            }
        } else {
            $historyRepository = $this->entityManager->getRepository(History::class);
            $histories = $historyRepository->findAll();
            $data = $this->convertToArray($histories);
        }

        return $data;
    }

    /**
     * Returns command history.
     *
     * @return array
     */
    public function findById($id): array
    {
        $history = $this->entityManager->find(History::class, $id);
        $data= [
            "id" => $history->getId(),
            "command" => $history->getCommand(),
            "description" => $history->getDescription(),
            "output" => $history->getOutput(),
            "result" => $history->getResult(),
            "time" => $history->getTime(),
        ];
        return $data;
    }

    public function deleteById($id)
    {
        try {
            $qb = $this->entityManager->createQueryBuilder();
            $qb->delete(History::class, 'h')
            ->where('h.id = ?0')
            ->setParameter(0, $id)
            ->getQuery()->getResult();
            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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
            // save to db
            $command["time"] = new \DateTime("now", new \DateTimeZone('Asia/Jakarta'));
            $history = new History();
            $history->setCommand($command['command']);
            $history->setDescription($command['description']);
            $history->setOutput($command['output']);
            $history->setResult($command['result']);
            $history->setTime($command["time"]);
            $this->entityManager->persist($history);
            $this->entityManager->flush();

            // save to file
            $command['id'] = $history->getId();
            $command['time'] = $history->getTime();
            $inp = file_get_contents('./Storage/db.json');
            $tempArray = json_decode($inp);
            if (!is_array($tempArray)) {
                $tempArray = [];
            }
            array_push($tempArray, $command);
            $jsonData = json_encode($tempArray);
            file_put_contents('./Storage/db.json', $jsonData);

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
            // clear all from database
            $qb = $this->entityManager->createQueryBuilder();
            $qb->delete(History::class)->getQuery()->getResult();

            // clear all from file
            file_put_contents('./Storage/db.json', '');

            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findByCommand(array $arguments, $driver): array
    {
        if ($driver == 'file') {
            $q = new Jsonq('./Storage/db.json');
            foreach ($arguments as $value) {
                $q->where('command', '=', ucfirst($value));
            }
            $histories = $q->get();

            if (!is_null($histories)) {
                $result = $histories;
            }
        } else {
            $qb = $this->entityManager->createQueryBuilder();
            $history = $qb->select('h')->from(History::class, 'h');
            foreach ($arguments as $key => $value) {
                $history = $history->orWhere("h.command = ?" . (string)$key)->setParameter($key, ucfirst($value));
            }
            $query = $history->getQuery();
            $result = $query->getResult();
            $result = $this->convertToArray($result);
        }

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
