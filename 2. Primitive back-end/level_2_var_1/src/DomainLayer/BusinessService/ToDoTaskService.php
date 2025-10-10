<?php

namespace App\DomainLayer\BusinessService;

use App\DataSourceLayer\DAO\ToDoTaskDAO as ToDoTaskDAO;
use App\DomainLayer\Entity\ToDoTask as ToDoTask;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;
use App\PresentationLayer\DataTransferObject\ToDoTaskDTO as ToDoTaskDTO;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;

class ToDoTaskService
{
    public function __construct(
        readonly string $fileName
    )
    {
    }

    /**
     * @return array
     * @throws AppException
     */
    public function getAll(): array
    {
        return new ToDoTaskDAO($this->fileName)->dataSet;
    }

    /**
     * @param ToDoTaskDTO $taskDTO
     * @return ToDoTask
     * @throws AppException
     */
    public function addTask(ToDoTaskDTO $taskDTO): ToDoTask
    {
        $newTask = ToDoTask::createNewToDoTask($taskDTO);
        new ToDoTaskDAO($this->fileName)->create($newTask);

        return $newTask;
    }

    /**
     * @param ToDoTaskDTO $taskDTO
     * @return ToDoTask
     * @throws AppException
     */
    public function changeTask(ToDoTaskDTO $taskDTO): ToDoTask
    {
        $dao = new ToDoTaskDAO($this->fileName);
        $task = $dao->findByKey($taskDTO->id);

        if ($task instanceof AbsentValue) {
            throw AppException::fromEnum(
                ex: AppExceptionsEnum::NotExistsInDatabase,
                details: ['Task with id ' . $taskDTO->id . ' NOT exists in DB'],
            );
        }

        if ($this->areEqual($task, $taskDTO)) {
            return $task;
        }

        $updatedTask = new ToDoTask($task->id, $taskDTO->text, $taskDTO->checked);
        $dao->update($updatedTask);

        return $updatedTask;
    }

    /**
     * @param ToDoTaskDTO $taskDTO
     * @return ToDoTask
     * @throws AppException
     */
    public function deleteTask(ToDoTaskDTO $taskDTO): ToDoTask
    {
        $dao = new ToDoTaskDAO($this->fileName);
        $deletedTask = $dao->findByKey($taskDTO->id);

        if ($deletedTask instanceof AbsentValue) {
            throw AppException::fromEnum(
                ex: AppExceptionsEnum::NotExistsInDatabase,
                details: ['Task with id ' . $taskDTO->id . ' NOT exists in DB'],
            );
        }

        $dao->delete($deletedTask);

        return $deletedTask;
    }

    /**
     * @param ToDoTask $entity
     * @param ToDoTaskDTO $dto
     * @return bool
     */
    private function areEqual(ToDoTask $entity, ToDoTaskDTO $dto): bool
    {
        return $entity->id === $dto->id
            && $entity->text === $dto->text
            && $entity->checked === $dto->checked;
    }
}