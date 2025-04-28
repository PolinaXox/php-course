<?php
// no usage partly
namespace App\ORM\DAO;

use App\DataSourceLayer\servicesDB\FileService as FileService;
use App\entities\ToDoTask as ToDoTask;
use App\ORM\dataMappers\ToDoTaskMapper as ToDoTaskMapper;
use App\PresentationLayer\DataTransferObjects\RequiredToDoTaskData as RequiredToDoTaskData;
use App\services\validationServices\UserToDoTaskDataValidatorService as Validator;
use Exception;

class ToDoTaskDAO implements RequiredToDoTaskData
{
    /**
     * @param ToDoTask $toDoTask
     * @param string $fileName
     * @return bool
     * @throws Exception
     */
    public function save(ToDoTask $toDoTask, string $fileName): bool
    {
        $arr = self::getTasksListAsArray($fileName);
        $arr[] = ToDoTaskMapper::mapToJsonFileItem($toDoTask);

        return FileService::rewriteFile($fileName, $arr);
    }

    /**
     * @param int $taskToDeleteId
     * @param string $fileName
     * @return bool
     * @throws Exception
     */
    public function delete(int $taskToDeleteId, string $fileName): bool
    {
        $arr = self::getTasksListAsArray($fileName);

        foreach ($arr as $key=>$item) {
            if($item['id'] == $taskToDeleteId) {
                unset($arr[$key]);
                break;
            }
        }

        return FileService::rewriteFile($fileName, array_values($arr));
    }

    /**
     * @param string $fileName
     * @return bool
     * @throws Exception
     */
    public function update(string $fileName): bool
    {
        $validator = new Validator();
        $arr = self::getTasksListAsArray($fileName);

        for ($ind = 0; $ind < count($arr); $ind++) {
            if($arr[$ind][self::ID] == $validator->getValidToDoTaskId()) {
                $arr[$ind][self::TEXT] = $validator->getValidToDoTaskText();
                $arr[$ind][self::STATE] = $validator->getValidToDoTaskState();
                break;
            }
        }

        return FileService::rewriteFile($fileName, array_values($arr));
    }

    /**
     * @param $fileName
     * @return array
     * @throws Exception
     */
    private function getTasksListAsArray($fileName): array
    {
        return FileService::getToDoListFileContentAsArray($fileName);
    }
}