<?php

namespace App\DataSourceLayer\DAO;

use App\DataSourceLayer\DataMapper\ToDoTaskMapper as ToDoTaskMapper;
use App\DataSourceLayer\ServiceDB\FileService as FileService;
use App\DomainLayer\Entity\ToDoTask as ToDoTask;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;
use App\PresentationLayer\DataTransferObject\ToDoTaskDTO as ToDoTaskDTO;

class ToDoTaskDAO
{
    const string TO_DO_LISTS_DIR = __DIR__ . '/../../../FileDB/toDoLists/';

    private string $filePath;
    private array $tasks;

    /**
     * @param string $fileName
     * @throws AppException
     */
    public function __construct(string $fileName)
    {
        $this->filePath = self::TO_DO_LISTS_DIR . $fileName;
        new FileService()->ensureFileExists($this->filePath);
        $this->tasks = json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    /**
     * @return string
     */
    // ++
    public function getAllTasksForFront(): string
    {
        return json_encode(['items' => array_values($this->tasks)]);
    }

    /**
     * @param ToDoTask $toDoTask
     * @return bool
     */
    public function save(ToDoTask $toDoTask): bool
    {
        $key = $toDoTask->id;
        $this->tasks[$key] = new ToDoTaskMapper()->mapToDatabaseRecord($toDoTask);

        return $this->saveChangesToDB();
    }

    /**
     * @return bool
     */
    private function saveChangesToDB(): bool
    {
        return file_put_contents(
            $this->filePath,
            json_encode($this->tasks, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK)
        );
    }

    /**
     * @param int $index
     * @return bool
     */
    public function delete(int $index): bool
    {
        if (!array_key_exists($index, $this->tasks)) {
            return false;
        }

        unset($this->tasks[$index]);

        return $this->saveChangesToDB();
    }

    /**
     * Task updating bases on ToDoTaskDTO obj, NOT on ToDoTask obj
     *
     * @param ToDoTaskDTO $taskDTO
     * @return bool
     * @throws AppException
     */
    public function update(ToDoTaskDTO $taskDTO): bool
    {
        $taskId = $taskDTO->id;

        if (!array_key_exists($taskId, $this->tasks)) {
            throw AppException::fromEnum(AppExceptionsList::DBRecordNotFound,
                ['filePath' => $this->filePath, 'taskId' => $taskId]);
        }

        $this->tasks[$taskId] = [
            'id' => $taskId,
            'text' => $taskDTO->text,
            'checked' => $taskDTO->checked,
        ];

        return $this->saveChangesToDB();
    }
}