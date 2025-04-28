<?php
// +
namespace App\PresentationLayer\DataTransferObjects;

abstract class UserInputDataDTO
{
    public array $rawInputData = [];

    /**
     * @param array $requiredInputDataKeys
     */
    public function __construct(private readonly array $requiredInputDataKeys)
    {
        $json = json_decode(file_get_contents("php://input"), true) ?? [];

        foreach ($this->requiredInputDataKeys as $key) {
            $this->rawInputData[$key] = $json[$key] ?? null;
        }
    }
}


