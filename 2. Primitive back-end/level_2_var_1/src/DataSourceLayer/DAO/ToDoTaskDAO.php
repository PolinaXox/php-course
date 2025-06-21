<?php

namespace App\DataSourceLayer\DAO;

use App\DataSourceLayer\DataMapper\ToDoTaskMapper as ToDoTaskMapper;
use App\DataSourceLayer\ServiceDB\FileService as FileService;
use App\DomainLayer\Entity\ToDoTask as ToDoTask;
use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\DataTransferObject\ToDoTaskDTO as ToDoTaskDTO;

//use Exception;

class ToDoTaskDAO
{
    const string TO_DO_LISTS_DIR = __DIR__ . '/../../../FileDB/toDoLists/';

    private string $filePath;
    private array $tasks;

    /**
     * @param string $fileName
     * @throws AppException
     */
    // ++
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
     * @param ToDoTaskDTO $taskDTO
     * @return bool
     */
    // ++
    public function update(ToDoTaskDTO $taskDTO): bool
    {
        // якщо запису немає - нічого не робити
        if (!array_key_exists($taskDTO->id, $this->tasks)) {
            return false;
        }

        // а ще можна через об'єкт
        // знайти запис по ід з ДТО -> записати запис з БД в об'єкт ->
        // -> змінити об'єкт -> записати об'єкт в БД
        $this->tasks[$taskDTO->id] = [
            'id' => $taskDTO->id,
            'text' => $taskDTO->text,
            'checked' => $taskDTO->checked,
        ];

        return $this->saveChangesToDB();
    }
}